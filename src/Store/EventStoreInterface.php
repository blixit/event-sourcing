<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Store;

use Blixit\EventSourcing\Aggregate\AggregateRootInterface;

interface EventStoreInterface //phpcs:ignore
{
    /**
     * @param mixed $aggregateId
     */
    public function get($aggregateId) : ?AggregateRootInterface;

    public function store(AggregateRootInterface &$aggregateRoot) : void;
}
