<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Event;

use Blixit\EventSourcing\Event\DataStructure\Payload;
use Blixit\EventSourcing\Utils\Accessor;

class EventAccessor extends Accessor
{
    /** @var EventAccessor $instance */
    protected static $instance;

    public function getPayload(EventInterface $event) : Payload
    {
        return $this->readProperty($event, 'payload');
    }

    /**
     * @return mixed
     */
    public function getAggregateId(EventInterface $event)
    {
        return $this->readProperty($event, 'aggregateId');
    }

    /**
     * @return mixed
     */
    public function getSequence(EventInterface $event)
    {
        return $this->readProperty($event, 'sequence');
    }

    public function setSequence(EventInterface $event, int $value) : void
    {
        $this->writeProperty($event, 'sequence', $value);
    }
}
