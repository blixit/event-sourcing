<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Store\SnapshotStore;

interface SnapshotInterface //phpcs:ignore
{
    public function getStreamName() : string;

    public function getPayload() : string;
}
