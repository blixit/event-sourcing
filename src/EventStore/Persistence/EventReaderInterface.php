<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\EventStore\Persistence;

use Blixit\EventSourcing\Event\EventInterface;
use Blixit\EventSourcing\Stream\StreamName;

interface EventReaderInterface //phpcs:ignore
{
    /**
     * @param mixed $aggregateId
     *
     * @return EventInterface[]
     */
    public function get($aggregateId) : array;

    /**
     * @return EventInterface[]
     */
    public function getByStream(StreamName $streamName) : array;

    /**
     * @return EventInterface[]
     */
    public function getByEvent(string $eventClassname) : array;
}
