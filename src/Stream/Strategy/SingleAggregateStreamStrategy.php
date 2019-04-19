<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Stream\Strategy;

use Blixit\EventSourcing\Stream\StreamName;

class SingleAggregateStreamStrategy extends StreamStrategy
{
    /**
     * @param mixed $aggregateId
     */
    public function computeName(string $aggregateClass, $aggregateId = null) : StreamName
    {
        return StreamName::fromString($aggregateClass);
    }
}
