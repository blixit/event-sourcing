<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Store\InMemory;

use Blixit\EventSourcing\Store\SnapshotStore\Snapshot;
use Blixit\EventSourcing\Store\SnapshotStore\SnapshotInterface;
use Blixit\EventSourcing\Store\SnapshotStore\SnapshotPersisterInterface;

class InMemorySnapshotPersister implements SnapshotPersisterInterface
{
    /** @var Snapshot[] $snapshots */
    private $snapshots = [];

    public function snapshot(SnapshotInterface $snapshot) : void
    {
        $this->snapshots[$snapshot->getStreamName()] = $snapshot;
    }

    /**
     * @param mixed $aggregateId
     */
    public function get(string $streamName) : ?SnapshotInterface
    {
        return $this->snapshots[$streamName] ?? null;
    }
}
