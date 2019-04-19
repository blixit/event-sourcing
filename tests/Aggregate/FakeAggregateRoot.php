<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Tests\Aggregate;

use Blixit\EventSourcing\Aggregate\AggregateRoot;
use Blixit\EventSourcing\Event\EventInterface;

class FakeAggregateRoot extends AggregateRoot
{
    public function apply(EventInterface $event) : void
    {
    }
}
