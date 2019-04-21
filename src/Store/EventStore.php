<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Store;

use Blixit\EventSourcing\Aggregate\AggregateAccessor;
use Blixit\EventSourcing\Aggregate\AggregateRoot;
use Blixit\EventSourcing\Aggregate\AggregateRootInterface;
use Blixit\EventSourcing\Event\EventAccessor;
use Blixit\EventSourcing\Event\EventInterface;
use Blixit\EventSourcing\Store\Exception\CorruptedReadEvent;
use Blixit\EventSourcing\Store\Exception\NonWritableEvent;
use Blixit\EventSourcing\Store\Persistence\EventPersisterException;
use Blixit\EventSourcing\Store\Persistence\EventPersisterInterface;
use Blixit\EventSourcing\Stream\ReadableStream;
use Blixit\EventSourcing\Stream\StorableStream;
use Blixit\EventSourcing\Stream\Strategy\StreamStrategy;
use Blixit\EventSourcing\Stream\StreamName;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Messenger\MessageBusInterface;
use Throwable;
use function sprintf;

class EventStore implements EventStoreInterface
{
    /** @var EventPersisterInterface $eventPersister */
    protected $eventPersister;

    /** @var string $aggregateClass */
    private $aggregateClass;

    /** @var StreamStrategy $streamStrategy */
    private $streamStrategy;

    /** @var MessageBusInterface $messageBus */
    private $messageBus;

    /** @var AggregateAccessor $aggregateAccessor */
    private $aggregateAccessor;

    /** @var EventAccessor $eventAccessor */
    private $eventAccessor;

    public function __construct(
        string $aggregateClass,
        EventPersisterInterface $eventPersister,
        string $streamStrategyClass,
        ?MessageBusInterface $messageBus = null
    ) {
        $this->eventPersister = $eventPersister;
        $this->aggregateClass = $aggregateClass;
        $this->streamStrategy = StreamStrategy::resolveStreamStrategy($streamStrategyClass);

        $this->messageBus = $messageBus;

        $this->aggregateAccessor = AggregateAccessor::getInstance();
        $this->eventAccessor     = EventAccessor::getInstance();
    }

    /**
     * @param mixed $aggregateId
     */
    public function getStreamNameForAggregateId($aggregateId = null) : StreamName
    {
        return $this->streamStrategy->computeName($this->aggregateClass, $aggregateId);
    }

    /**
     * @param mixed $aggregateId
     *
     * @throws ReflectionException
     * @throws CorruptedReadEvent
     */
    public function get($aggregateId) : ?AggregateRootInterface
    {
        /** @var AggregateRootInterface $aggregate */
        $aggregate = $this->buildAggregate($aggregateId);
        // compute streamName based on stream strategy
        $streamName = $this->getStreamNameForAggregateId($aggregateId);
        // get events from store
        $events = $this->eventPersister->getByStream($streamName, $aggregate->getSequence());
        // get beforeRead callback
        $beforeRead = [$this, 'beforeRead'];
        // build stream
        $stream = new ReadableStream(
            $streamName,
            $events,
            static function (EventInterface $event) use ($beforeRead, $aggregate) : void {
                $beforeRead($aggregate, $event);
            }
        );

        // replay events
        foreach ($stream->getIterator() as $event) {
            /** @var EventInterface $event */
            // ignores not relevant events
            if ($event->getAggregateId() !== $aggregateId) {
                continue;
            }

            // ignore event with bad type
            if ($this->aggregateClass !== $event->getAggregateClass()) {
                continue;
            }

            /** @var AggregateRoot $aggregate */
            $aggregate->apply($event);
            $this->aggregateAccessor->setVersionSequence($aggregate, $event->getSequence());
            $this->afterRead($aggregate, $event);
        }

        return $aggregate->getSequence() > 0 ? $aggregate : null;
    }

    /**
     * @param mixed $aggregateId
     *
     * @throws ReflectionException
     */
    protected function getEmptyAggregate($aggregateId) : AggregateRootInterface
    {
        /** @var AggregateRootInterface $aggregate */
        $aggregate = (new ReflectionClass($this->aggregateClass))->newInstanceWithoutConstructor();
        $this->aggregateAccessor->setAggregateId($aggregate, $aggregateId);
        return $aggregate;
    }

    /**
     * @param mixed $aggregateId
     *
     * @throws ReflectionException
     */
    protected function buildAggregate($aggregateId) : AggregateRootInterface
    {
        return $this->getEmptyAggregate($aggregateId);
    }

    /**
     * @throws EventPersisterException
     * @throws NonWritableEvent
     */
    public function store(AggregateRootInterface &$aggregateRoot) : void
    {
        // compute streamName based on stream strategy
        $streamName = $this->getStreamNameForAggregateId($aggregateRoot->getAggregateId());
        // get beforeWrite callback
        $beforeWrite = [$this, 'beforeWrite'];

        /** @var AggregateRoot $aggregateRoot */
        $stream = new StorableStream(
            $streamName,
            $aggregateRoot->getRecordedEvents(),
            static function (EventInterface $event) use ($beforeWrite, $aggregateRoot) : void {
                $beforeWrite($aggregateRoot, $event);
            }
        );

        foreach ($stream->getIterator() as $event) {
            $this->writeLoopIteration($aggregateRoot, $event);
            // dispatch event
            if (empty($this->messageBus)) {
                continue;
            }
            $this->messageBus->dispatch($event);
        }
    }

    /**
     * @throws EventPersisterException
     */
    protected function writeLoopIteration(AggregateRootInterface &$aggregateRoot, EventInterface &$event) : void
    {
        // next sequence number
        $nextSequence = $aggregateRoot->getSequence() + 1;
        // set event sequence
        $this->eventAccessor->setSequence($event, $nextSequence);
        // set event aggregate class
        $this->eventAccessor->setAggregateClass($event, $this->aggregateClass);
        // save event
        $committedEvent = $this->persist($event);
        // remove from recorded events
        $this->aggregateAccessor->shiftEvent($aggregateRoot);
        // if persistence works, then increment aggregate sequence
        $this->aggregateAccessor->setVersionSequence($aggregateRoot, $nextSequence);
        // apply event
        $aggregateRoot->apply($committedEvent);
        // do something with the updated aggregate like snapShotting
        $this->afterWrite($aggregateRoot, $committedEvent);
    }

    /**
     * @throws EventPersisterException
     */
    protected function persist(EventInterface $event) : EventInterface
    {
        try {
            return clone $this->eventPersister->persist($event);
        } catch (Throwable $exception) {
            throw new EventPersisterException($exception->getMessage());
        }
    }

    /**
     * @param mixed $aggregateId
     *
     * @throws NonWritableEvent
     */
    protected function beforeWrite(AggregateRootInterface $aggregateRoot, EventInterface $event) : void
    {
        if ($event->getAggregateId() !== $aggregateRoot->getAggregateId()) {
            throw new NonWritableEvent(sprintf(
                'Event aggregate Id not expected. Expected: %s . Found: %s',
                $aggregateRoot->getAggregateId(),
                $event->getAggregateId()
            ));
        }
        if (! empty($event->getSequence())) {
            throw new NonWritableEvent('Sequence number should be empty. Found: ' . $event->getSequence());
        }
        if (! empty($event->getStreamName())) {
            throw new NonWritableEvent('Stream name should be empty. Found: ' . $event->getStreamName());
        }
    }

    protected function afterWrite(AggregateRootInterface $aggregateRoot, EventInterface $event) : void
    {
    }

    protected function beforeRead(AggregateRootInterface $aggregateRoot, EventInterface $event) : void
    {
    }

    protected function afterRead(AggregateRootInterface $aggregateRoot, EventInterface $event) : void
    {
    }
}
