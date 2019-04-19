<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Aggregate\Domain;

use Blixit\EventSourcing\Event\Event;

class DomainEvent extends Event
{
    /**
     * Keeps the sequence number of the associated aggregate
     *
     * @var mixed $version
     */
    private $version;

    public function assignVersionFrom(AggregateRootInterface $aggregateRoot) : void
    {
    }
}
