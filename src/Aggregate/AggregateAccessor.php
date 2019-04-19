<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Aggregate;

use Blixit\EventSourcing\Event\EventInterface;
use Blixit\EventSourcing\Utils\Accessor;
use function array_shift;

class AggregateAccessor extends Accessor
{
    /** @var AggregateAccessor $instance */
    protected static $instance;

    /**
     * @param mixed $value
     */
    public function setAggregateId(AggregateRootInterface &$aggregateRoot, $value) : void
    {
        $this->writeProperty($aggregateRoot, 'aggregateId', $value);
    }

    public function getVersionSequence(AggregateRootInterface $aggregateRoot) : int
    {
        return $this->readProperty($aggregateRoot, 'versionSequence');
    }

    public function setVersionSequence(AggregateRootInterface &$aggregateRoot, int $value) : void
    {
        $this->writeProperty($aggregateRoot, 'versionSequence', $value);
    }

    public function shiftEvent(AggregateRootInterface &$aggregateRoot) : ?EventInterface
    {
        /** @var AggregateRoot $aggregateRoot */
        $events = $aggregateRoot->getRecordedEvents();
        $event  = array_shift($events);
        if (! empty($event)) {
            $this->writeProperty($aggregateRoot, 'recordedEvents', $events);
        }
        return $event;
    }
}
