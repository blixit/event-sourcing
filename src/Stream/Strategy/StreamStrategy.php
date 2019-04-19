<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Stream\Strategy;

use Blixit\EventSourcing\Stream\StreamName;

abstract class StreamStrategy
{
    public const DEFAULT_NAME = 'event_stream';

    /**
     * @param mixed $aggregateId
     */
    abstract public function computeName(string $aggregateClass, $aggregateId = null) : StreamName;

    public static function resolveStreamStrategy(string $streamStrategyClass) : StreamStrategy
    {
        return new $streamStrategyClass();
    }
}
