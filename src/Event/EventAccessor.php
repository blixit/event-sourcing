<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Event;

use Blixit\EventSourcing\Event\DataStructure\Payload;
use Blixit\EventSourcing\Utils\Accessor;

class EventAccessor extends Accessor
{
    /** @var EventAccessor $instance */
    protected static $instance;

    public function getRealPayload(EventInterface $event) : Payload
    {
        return $this->readProperty($event, 'payload');
    }

    public function setSequence(EventInterface &$event, int $value) : void
    {
        $this->writeProperty($event, 'sequence', $value);
    }
}
