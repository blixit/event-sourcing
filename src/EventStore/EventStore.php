<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\EventStore;

use Blixit\EventSourcing\Aggregate\AggregateAccessor;
use Blixit\EventSourcing\Aggregate\AggregateRoot;
use Blixit\EventSourcing\Aggregate\AggregateRootInterface;
use Blixit\EventSourcing\Aggregate\Replay\EventPlayer;
use Blixit\EventSourcing\Event\EventAccessor;
use Blixit\EventSourcing\Event\EventInterface;
use Blixit\EventSourcing\EventStore\Persistence\EventPersisterException;
use Blixit\EventSourcing\EventStore\Persistence\EventPersisterInterface;
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

    public function __construct(
        string $aggregateClass,
        EventPersisterInterface $eventPersister
    ) {
        if (empty($this->eventPlayer)) {
            $this->eventPlayer = EventPlayer::getInstance();
        }

        $this->eventPersister = $eventPersister;
        $this->aggregateClass = $aggregateClass;
    }

    /**
     * @param mixed $aggregateId
     *
     * @throws ReflectionException
     */
    public function get($aggregateId) : ?AggregateRootInterface
    {
        // compute streamName based on stream strategy
        $streamName = StreamName::fromString('mystream');

        // get events from store ...
//        $events = $this->eventPersister->getByStream($streamName);
        $events = $this->eventPersister->get($aggregateId);

        $stream = new Stream($streamName, $events);

        // implements snapshot

        return $this->eventPlayer->replay($stream, $this->aggregateClass, $aggregateId, 0);
    }

    /**
     * @throws EventPersisterException
     */
    public function store(AggregateRootInterface &$aggregateRoot) : void
    {
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
