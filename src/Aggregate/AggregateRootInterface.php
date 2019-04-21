<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Aggregate;

use Blixit\EventSourcing\Event\EventInterface;

interface AggregateRootInterface extends BaseAggregateInterface //phpcs:ignore
{
    public function getSequence() : int;

    public function apply(EventInterface $event) : void;
}
