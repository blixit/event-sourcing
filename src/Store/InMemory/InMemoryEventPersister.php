<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Store\InMemory;

use Blixit\EventSourcing\Event\EventInterface;
use Blixit\EventSourcing\Store\Persistence\EventPersisterInterface;
use Blixit\EventSourcing\Stream\StreamName;
use function array_filter;

class InMemoryEventPersister implements EventPersisterInterface
{
    /** @var EventInterface[] $events */
    private $events = [];

    /**
     * @return EventInterface[]
     */
    public function getByStream(StreamName $streamName, ?int $fromSequence = 0) : array
    {
        return array_filter($this->events, static function (EventInterface $event) use (
            $streamName,
            $fromSequence
        ) {
            return $event->getStreamName() === (string) $streamName && $event->getSequence() > $fromSequence;
        });
    }

    public function persist(EventInterface $event) : EventInterface
    {
        $this->events[] = $event;

        return $event;
    }
}
