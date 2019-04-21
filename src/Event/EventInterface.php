<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Event;

interface EventInterface //phpcs:ignore
{
    /**
     * @param mixed[] $payload
     */
    public static function occur(string $id, array $payload) : EventInterface;

    /**
     * @return mixed
     */
    public function getAggregateId();

    public function getSequence() : int;

    public function getAggregateClass() : ?string;

    public function getTimestamp() : int;

    public function getStreamName() : ?string;

    /**
     * @return mixed[]
     */
    public function getPayload() : array;
}
