<?php

declare(strict_types=1);

namespace Blixit\EventSourcingTests\Event;

use Blixit\EventSourcing\Event\Event;

class FakeEvent extends Event
{
    public function getIncrement() : int
    {
        return $this->payload['increment'] ?? 0;
    }
}
