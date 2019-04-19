<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Event;

use Blixit\EventSourcing\Event\DataStructure\Payload;
use function time;

/**
 * Class DomainEvent
 *
 * @link    http://github.com/blixit
 */
class Event implements EventInterface
{
    /** @var mixed $id */
    protected $id;

    /** @var Payload $payload */
    protected $payload;

    /** @var mixed $aggregateId */
    protected $aggregateId;

    /** @var int $timestamp */
    protected $timestamp;

    /** @var int $sequence */
    protected $sequence = 0;

    /**
     * @param mixed[] $payload
     */
    protected function __construct(string $aggregateId, Payload $payload)
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
        return new $lsbClass($aggregateId, Payload::fromArray($payload));
    }
//
//    protected static function occurring(string $aggregateId, Payload $payload) : EventInterface
//    {
//        $lsbClass = static::class;
//        return new $lsbClass($aggregateId, $payload);
//    }

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

    public function getTimestamp() : int
    {
        return $this->timestamp;
    }

    /**
     * @return mixed[]
     */
    public function getPayload() : array
    {
        return $this->payload->getArrayCopy();
    }
}
