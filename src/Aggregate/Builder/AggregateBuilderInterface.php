<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Aggregate\Builder;

use Blixit\EventSourcing\Aggregate\AggregateRootInterface;
use Blixit\EventSourcing\Stream\Stream;

interface AggregateBuilderInterface //phpcs:ignore
{
    /**
     * @param mixed $aggregateId
     */
    public function build(Stream $stream, $aggregateId, string $type) : AggregateRootInterface;
}
