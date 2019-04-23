<?php

declare(strict_types=1);

namespace Blixit\EventSourcingTests\Store;

use Blixit\EventSourcing\Event\EventInterface;
use Blixit\EventSourcing\Store\Matcher\EventMatcherInterface;
use Blixit\EventSourcing\Store\Persistence\EventPersisterInterface;
use Blixit\EventSourcing\Stream\StreamName;

class FakeEventPersister implements EventPersisterInterface
{
    public function persist(EventInterface $event) : EventInterface
    {
        return $event;
    }

    /**
     * @param mixed $aggregateId
     *
     * @return EventInterface[]
     */
    public function get($aggregateId) : array
    {
        return [];
    }

    /**
     * @return EventInterface[]
     */
    public function getByStream(StreamName $streamName, ?int $fromSequence = 0) : array
    {
        return [];
    }

    /**
     * @return EventInterface[]
     */
    public function getByEvent(string $eventClassname) : array
    {
        return [];
    }

    /**
     * @return EventInterface[]
     */
    public function find(EventMatcherInterface $eventMatcher) : array
    {
        return [];
    }
}
