<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Store\SnapshotStore;

use Exception;

class SnapshotConfiguration
{
    /**
     * The number of events between each snapshot
     *
     * @var int $steps
     */
    private $steps;

    /**
     * The snapshot class to instantiante when storing the snapshot objects
     *
     * @var string $snapshotClass
     */
    private $snapshotClass;

    /**
     * @throws Exception
     */
    public function __construct(int $steps, string $snapshotClass)
    {
        if ($steps <= 0) {
            throw new Exception('The snapshot step should be greater than 0');
        }
        $this->snapshotClass = empty($snapshotClass) ? Snapshot::class : $snapshotClass;
        $this->steps         = $steps;
    }

    public function getSteps() : int
    {
        return $this->steps;
    }

    public function getSnapshotClass() : string
    {
        return $this->snapshotClass;
    }
}
