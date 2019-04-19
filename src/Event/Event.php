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
    /** @var mixed $uuid */
    protected $uuid;

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
    protected function __construct(string $id, Payload $payload)
    {
        $this->aggregateId = $id;
        $this->payload     = $payload;
        $this->timestamp   = time();
    }

    /**
     * @param mixed[] $payload
     */
    public static function occur(string $id, array $payload) : EventInterface
    {
        $lsbClass = static::class;
        return $lsbClass::occurring($id, Payload::fromArray($payload));
    }

    protected static function occurring(string $id, Payload $payload) : EventInterface
    {
        $lsbClass = static::class;
        return new $lsbClass($id, $payload);
    }

    /**
     * @return mixed[]
     */
    public function getPayload() : array
    {
        return $this->payload->getArrayCopy();
    }
}
