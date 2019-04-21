<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Store\Persistence;

use Blixit\EventSourcing\Event\EventInterface;

interface EventWriterInterface //phpcs:ignore
{
    public function persist(EventInterface $event) : EventInterface;
}
