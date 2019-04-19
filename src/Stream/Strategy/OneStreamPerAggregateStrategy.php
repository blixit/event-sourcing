<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Stream\Strategy;

use Blixit\EventSourcing\Stream\StreamName;
use function sprintf;

class OneStreamPerAggregateStrategy extends StreamStrategy
{
    /**
     * @param mixed $aggregateId
     */
    public function computeName(string $aggregateClass, $aggregateId = null) : StreamName
    {
        return StreamName::fromString(sprintf(
            '%s.%s',
            $aggregateClass,
            $aggregateId
        ));
    }
}
