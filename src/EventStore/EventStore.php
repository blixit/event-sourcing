<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\EventStore;

use Blixit\EventSourcing\Aggregate\AggregateAccessor;
use Blixit\EventSourcing\Aggregate\AggregateRoot;
use Blixit\EventSourcing\Aggregate\AggregateRootInterface;
use Blixit\EventSourcing\Event\EventAccessor;
use Blixit\EventSourcing\Event\EventInterface;
use Blixit\EventSourcing\EventStore\Persistence\EventPersisterException;
use Blixit\EventSourcing\EventStore\Persistence\EventPersisterInterface;
use Blixit\EventSourcing\Stream\Replay\EventPlayer;
use Blixit\EventSourcing\Stream\Strategy\StreamStrategy;
use Blixit\EventSourcing\Stream\Stream;
use Blixit\EventSourcing\Stream\StreamName;
use ReflectionException;
use Symfony\Component\Messenger\MessageBusInterface;
use Throwable;

class EventStore implements EventStoreInterface
{
    /** @var EventPlayer $eventPlayer */
    private $eventPlayer;

    /** @var EventPersisterInterface $eventPersister */
    private $eventPersister;

    /** @var string $aggregateClass */
    private $aggregateClass;

    /** @var StreamStrategy $streamStrategy */
    private $streamStrategy;

    /** @var AggregateAccessor $aggregateAccessor */
    private $aggregateAccessor;

    /** @var EventAccessor $eventAccessor */
    private $eventAccessor;

    public function __construct(
        string $aggregateClass,
        EventPersisterInterface $eventPersister,
        string $streamStrategyClass//,
//        MessageBusInterface $messageBus
    ) {
        if (empty($this->eventPlayer)) {
            $this->eventPlayer = EventPlayer::getInstance();
        }

        $this->eventPersister = $eventPersister;
        $this->aggregateClass = $aggregateClass;
        $this->streamStrategy = StreamStrategy::resolveStreamStrategy($streamStrategyClass);

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
     */
    public function get($aggregateId) : ?AggregateRootInterface
    {
        // compute streamName based on stream strategy
        $streamName = $this->getStreamNameForAggregateId($aggregateId);
        // get events from store ...
        $events = $this->eventPersister->getByStream($streamName);

        // build stream
        $stream = new Stream($streamName, $events);

        // implements snapshot

        return $this->eventPlayer->replay($stream, $this->aggregateClass, $aggregateId, 0);
    }

    /**
     * @throws EventPersisterException
     */
    public function store(AggregateRootInterface &$aggregateRoot) : void
    {
        $streamName = null;

        /** @var AggregateRoot $aggregateRoot */
        foreach ($aggregateRoot->getRecordedEvents() as $event) {
            /** @var EventInterface $event */
            if ($event->getSequence() > AggregateRoot::DEFAULT_SEQUENCE_POSITION) {
                throw new EventReplicationAttempted($event);
            }
            if (empty($streamName)) {
                // compute streamName based on stream strategy
                $streamName = $this->getStreamNameForAggregateId($event->getAggregateId());
            }
            // set stream name for event
            $this->eventAccessor->setStreamName($event, (string) $streamName);

            // next sequence
            $nextSequence = $aggregateRoot->getSequence() + 1;
            $this->eventAccessor->setSequence($event, $nextSequence);

            // save event
            try {
                $committedEvent = clone $this->eventPersister->persist($event);
            } catch (Throwable $exception) {
                throw new EventPersisterException($exception->getMessage());
            }
            // remove from recorded events
            $this->aggregateAccessor->shiftEvent($aggregateRoot);
            // if persistence works, then increment aggregate sequence
            $this->aggregateAccessor->setVersionSequence($aggregateRoot, $nextSequence);
            // dispatch event
        }
    }
}
