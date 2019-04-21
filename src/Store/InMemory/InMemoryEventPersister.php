<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Store\InMemory;

use Blixit\EventSourcing\Event\EventAccessor;
use Blixit\EventSourcing\Event\EventInterface;
use Blixit\EventSourcing\Store\Persistence\EventPersisterInterface;
use Blixit\EventSourcing\Stream\StreamName;
use function array_filter;
use function uniqid;

class InMemoryEventPersister implements EventPersisterInterface
{
    /** @var EventInterface[] $events */
    private $events = [];

    /**
     * @param mixed $aggregateId
     *
     * @return EventInterface[]
     */
    public function get($aggregateId) : array
    {
        return array_filter($this->events, static function (EventInterface $event) use ($aggregateId) {
            return $event->getAggregateId() === $aggregateId;
        });
    }

    /**
     * @return EventInterface[]
     */
    public function getByStream(StreamName $streamName, ?int $fromSequence = 0) : array
    {
        return array_filter($this->events, static function (EventInterface $event) use (
            $streamName,
            $fromSequence
        ) {
            return $event->getStreamName() === (string) $streamName && $event->getSequence() >= $fromSequence;
        });
    }

    /**
     * @return EventInterface[]
     */
    public function getByEvent(string $eventClassname) : array
    {
        return [];
    }

    public function persist(EventInterface $event) : EventInterface
    {
        /** @var EventAccessor $accessor */
        $accessor = EventAccessor::getInstance();
        $accessor->setId($event, uniqid($event->getAggregateId()));
        $this->events[] = $event;

        return $event;
    }
}
