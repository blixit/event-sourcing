<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Store\Persistence;

use Blixit\EventSourcing\Event\EventInterface;
use Blixit\EventSourcing\Store\Matcher\EventMatcherInterface;
use Blixit\EventSourcing\Stream\StreamName;

interface EventReaderInterface //phpcs:ignore
{
    /**
     * @return EventInterface[]
     */
    public function getByStream(StreamName $streamName, ?int $fromSequence = 0) : array;

    /**
     * @return EventInterface[]
     */
    public function find(EventMatcherInterface $eventMatcher) : array;
}
