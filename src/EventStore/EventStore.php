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

    public function __construct(
        string $aggregateClass,
        EventPersisterInterface $eventPersister,
        string $streamStrategyClass
    ) {
        if (empty($this->eventPlayer)) {
            $this->eventPlayer = EventPlayer::getInstance();
        }

        $this->eventPersister = $eventPersister;
        $this->aggregateClass = $aggregateClass;
        $this->streamStrategy = StreamStrategy::resolveStreamStrategy($streamStrategyClass);
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
        // compute streamName based on stream strategy
        $streamName = $this->getStreamNameForAggregateId($aggregateRoot->getAggregateId());

        /** @var AggregateAccessor $aggAccessor */
        $aggAccessor = AggregateAccessor::getInstance();
        /** @var EventAccessor $evAccessor */
        $evAccessor = EventAccessor::getInstance();

        /** @var AggregateRoot $aggregateRoot */
        foreach ($aggregateRoot->getRecordedEvents() as $event) {
            /** @var EventInterface $event */
            if ($event->getSequence() > AggregateRoot::DEFAULT_SEQUENCE_POSITION) {
                throw new EventReplicationAttempted($event);
            }
            // next sequence
            $nextSequence = $aggregateRoot->getSequence() + 1;
            $evAccessor->setSequence($event, $nextSequence);
            $evAccessor->setStreamName($event, (string) $streamName);

            // save event
            try {
                $committedEvent = clone $this->eventPersister->persist($event);
            } catch (Throwable $exception) {
                throw new EventPersisterException($exception->getMessage());
            }
            // remove from recorded events
            $aggAccessor->shiftEvent($aggregateRoot);
            // if persistence works, then increment aggregate sequence
            $aggAccessor->setVersionSequence($aggregateRoot, $nextSequence);
            // dispatch event
        }
    }
}
