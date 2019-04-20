<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Stream\Strategy;

use Blixit\EventSourcing\Stream\StreamName;
use function sprintf;

class OneStreamPerAggregateStrategy extends StreamStrategy
{
    /**
     * @param mixed $aggregateId
     *
     * @throws NamingStrategyException
     */
    public function computeName(string $aggregateClass, $aggregateId = null) : StreamName
    {
        if (empty($aggregateId)) {
            throw new NamingStrategyException('OneStreamPerAggregateStrategy requires not blank aggregate id');
        }
        return StreamName::fromString(sprintf(
            '%s.%s',
            $aggregateClass,
            $aggregateId
        ));
    }
}
