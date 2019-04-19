<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\EventStore\Persistence;

use Blixit\EventSourcing\Event\EventInterface;

interface EventWriterInterface //phpcs:ignore
{
    public function persist(EventInterface $event) : EventInterface;
}
