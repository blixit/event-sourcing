<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Aggregate;

use Blixit\EventSourcing\Event\EventAccessor;
use Blixit\EventSourcing\Event\EventInterface;

abstract class AggregateRoot implements AggregateRootInterface
{
    public const DEFAULT_SEQUENCE_POSITION = 0;

    /** @var mixed $aggregateId */
    protected $aggregateId;

    /** @var int $versionSequence */
    protected $versionSequence = self::DEFAULT_SEQUENCE_POSITION; // phpcs:ignore

    /** @var EventInterface[] $recordedEvents */
    protected $recordedEvents = [];

    /** @var EventAccessor $eventAccessor */
    protected $eventAccessor;

    /**
     * @param mixed $aggregateId
     */
    public function setAggregateId($aggregateId) : void
    {
        $this->aggregateId = $aggregateId;
    }

    /**
     * @return mixed
     */
    public function getAggregateId()
    {
        return $this->aggregateId;
    }

    public function getSequence() : int
    {
        return $this->versionSequence;
    }

    public function record(EventInterface $event) : void
    {
        $this->recordedEvents[] = $event;
    }

    /**
     * @return EventInterface[]
     */
    public function getRecordedEvents() : array
    {
        return $this->recordedEvents;
    }

    public function apply(EventInterface $event) : void
    {
    }
}
