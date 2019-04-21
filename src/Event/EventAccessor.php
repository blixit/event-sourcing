<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Event;

use Blixit\EventSourcing\Utils\Accessor;

class EventAccessor extends Accessor
{
    /** @var EventAccessor $instance */
    protected static $instance;

    /**
     * @param mixed $value
     */
    public function setId(EventInterface &$event, $value) : void
    {
        $this->writeProperty($event, 'id', $value);
    }

    public function setSequence(EventInterface &$event, int $value) : void
    {
        $this->writeProperty($event, 'sequence', $value);
    }

    public function setAggregateClass(EventInterface &$event, string $value) : void
    {
        $this->writeProperty($event, 'aggregateClass', $value);
    }

    public function setStreamName(EventInterface &$event, string $value) : void
    {
        $this->writeProperty($event, 'streamName', $value);
    }
}
