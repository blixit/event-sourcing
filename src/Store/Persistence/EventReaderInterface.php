<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Store\Persistence;

use Blixit\EventSourcing\Event\EventInterface;
use Blixit\EventSourcing\Store\Matcher\EventMatcherInterface;
use Blixit\EventSourcing\Stream\StreamName;

interface EventReaderInterface //phpcs:ignore
{
    /**
     * Return the list of events that match the following conditions:
     * - event->streamName == (string) $streamName
     * - event->sequence >= 0
     *
     * @return EventInterface[]
     */
    public function getByStream(StreamName $streamName, ?int $fromSequence = 0) : array;

    /**
     * Returns the last event stored into the stream
     */
    public function getLastEvent(StreamName $streamName) : ?EventInterface;

    /**
     * @return EventInterface[]
     */
    public function find(EventMatcherInterface $eventMatcher) : array;
}
