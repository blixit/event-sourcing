<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Store\SnapshotStore;

interface SnapshotPersisterInterface //phpcs:ignore
{
    public function snapshot(SnapshotInterface $aggregateRoot) : void;

    /**
     * @param mixed $aggregateId
     */
    public function get($aggregateId) : ?SnapshotInterface;
}
