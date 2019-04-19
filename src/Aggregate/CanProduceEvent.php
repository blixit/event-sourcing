<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Aggregate;

use Blixit\EventSourcing\Event\EventInterface;

interface CanProduceEvent
{
    public function record(EventInterface $event) : void;

    /**
     * @return EventInterface[]
     */
    public function getRecordedEvents() : array;
}
