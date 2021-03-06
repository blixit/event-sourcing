<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Event;

use function time;

/**
 * Class DomainEvent
 *
 * @link    http://github.com/blixit
 */
class Event implements EventInterface
{
    /** @var mixed[] $payload */
    protected $payload = [];

    /** @var string $streamName */
    protected $streamName;

    /** @var mixed $aggregateId */
    protected $aggregateId;

    /** @var mixed $aggregateClass */
    protected $aggregateClass;

    /** @var int $timestamp */
    protected $timestamp;

    /** @var int $sequence */
    protected $sequence = 0;

    /**
     * @param mixed[] $payload
     */
    protected function __construct(string $aggregateId, array $payload)
    {
        $this->aggregateId = $aggregateId;
        $this->payload     = $payload;
        $this->timestamp   = time();
    }

    /**
     * @param mixed[] $payload
     */
    public static function occur(string $aggregateId, array $payload) : EventInterface
    {
        $lsbClass = static::class;
        return new $lsbClass($aggregateId, $payload);
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
        return $this->sequence;
    }

    public function getAggregateClass() : ?string
    {
        return $this->aggregateClass;
    }

    public function getTimestamp() : int
    {
        return $this->timestamp;
    }

    public function getStreamName() : ?string
    {
        return $this->streamName;
    }

    /**
     * @return mixed[]
     */
    public function getPayload() : array
    {
        return $this->payload;
    }
}
