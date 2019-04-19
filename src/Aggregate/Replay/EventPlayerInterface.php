<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Aggregate\Replay;

use Blixit\EventSourcing\Aggregate\AggregateRootInterface;
use Blixit\EventSourcing\Stream\Stream;

interface EventPlayerInterface //phpcs:ignore
{
    /**
     * @param mixed $aggregateId
     */
    public function replay(
        Stream $stream,
        string $aggregateClass,
        $aggregateId,
        ?int $initialPosition,
        ?string $eventType
    ) : ?AggregateRootInterface;

    /**
     * @param mixed $aggregateId
     */
    public function replayFromAggregate(
        Stream $stream,
        AggregateRootInterface $aggregate,
        $aggregateId,
        ?int $initialPosition = 0,
        ?string $eventType = null
    ) : ?AggregateRootInterface;
}
