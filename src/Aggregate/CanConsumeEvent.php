<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Aggregate;

use Blixit\EventSourcing\Event\EventInterface;

interface CanConsumeEvent
{
    public function apply(EventInterface $event) : void;
}
