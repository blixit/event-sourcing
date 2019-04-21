<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Store\SnapshotStore;

interface SnapshotPersisterInterface //phpcs:ignore
{
    public function snapshot(SnapshotInterface $snapshot) : void;

    public function get(string $streamName) : ?SnapshotInterface;
}
