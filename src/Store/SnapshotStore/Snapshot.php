<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Store\SnapshotStore;

class Snapshot implements SnapshotInterface
{
    /** @var string $streamName */
    protected $streamName;

    /** @var string $payload */
    protected $payload;

    public function __construct(string $streamName, string $payload)
    {
        $this->streamName = $streamName;
        $this->payload    = $payload;
    }

    public function getStreamName() : string
    {
        return $this->streamName;
    }

    public function getPayload() : string
    {
        return $this->payload;
    }
}
