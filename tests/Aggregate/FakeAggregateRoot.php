<?php

declare(strict_types=1);

namespace Blixit\EventSourcingTests\Aggregate;

use Blixit\EventSourcing\Aggregate\AggregateRoot;
use Blixit\EventSourcing\Event\EventInterface;
use Blixit\EventSourcingTests\Event\FakeEvent;

class FakeAggregateRoot extends AggregateRoot
{
    /** @var int $property */
    private $property = 0;

    /**
     * @param mixed $aggregateId
     */
    public function __construct($aggregateId)
    {
        $this->setAggregateId($aggregateId);
    }

    public function apply(EventInterface $event) : void
    {
        switch (true) {
            case $event instanceof FakeEvent:
                $this->property += $event->getIncrement();
                break;
            default:
        }
    }

    public function getProperty() : int
    {
        return $this->property;
    }
}
