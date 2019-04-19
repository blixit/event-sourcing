<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Aggregate;

use Blixit\EventSourcing\Event\EventInterface;

interface AggregateRootInterface //phpcs:ignore
{
    public static function getInstance() : AggregateRootInterface;

    /**
     * @return mixed
     */
    public function getAggregateId();

    public function apply(EventInterface $event) : void;
}
