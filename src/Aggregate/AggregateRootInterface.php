<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Aggregate;

use Blixit\EventSourcing\Event\EventInterface;

interface AggregateRootInterface extends BaseAggregateInterface //phpcs:ignore
{
    public function getSequence() : int;

    public function apply(EventInterface $event) : void;

    public function record(EventInterface $event) : void;

    /**
     * @return EventInterface[]
     */
    public function getRecordedEvents() : array;
}
