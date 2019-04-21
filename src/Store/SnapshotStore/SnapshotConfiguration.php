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
     * @throws Exception
     */
    public function __construct(int $steps)
    {
        if ($steps <= 0) {
            throw new Exception('The snapshot step should be greater than 0');
        }
        $this->steps = $steps;
    }

    public function getSteps() : int
    {
        return $this->steps;
    }
}
