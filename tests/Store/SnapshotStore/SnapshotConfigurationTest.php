<?php

declare(strict_types=1);

namespace Blixit\EventSourcingTests\Store\SnapshotStore;

use Blixit\EventSourcing\Store\SnapshotStore\Snapshot;
use Blixit\EventSourcing\Store\SnapshotStore\SnapshotConfiguration;
use Exception;
use PHPUnit\Framework\TestCase;
use Throwable;

class SnapshotConfigurationTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testConfiguration() : void
    {
        $configuration = new SnapshotConfiguration(10, Snapshot::class);
        $this->assertSame(10, $configuration->getSteps());

        try {
            new SnapshotConfiguration(-1, Snapshot::class);
        } catch (Throwable $exception) {
            $this->assertInstanceOf(Throwable::class, $exception);
        }
        try {
            new SnapshotConfiguration(0, Snapshot::class);
        } catch (Throwable $exception) {
            $this->assertInstanceOf(Throwable::class, $exception);
        }
    }
}
