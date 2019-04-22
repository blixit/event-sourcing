<?php

declare(strict_types=1);

namespace Blixit\EventSourcingTests\Store\SnapshotStore;

use Blixit\EventSourcing\Store\SnapshotStore\SnapshotConfiguration;
use Exception;
use PHPUnit\Framework\TestCase;

class SnapshotConfigurationTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testConfiguration() : void
    {
        $configuration = new SnapshotConfiguration(10);
        $this->assertSame(10, $configuration->getSteps());

        $this->expectException(Exception::class); //phpcs:ignore
        new SnapshotConfiguration(-1);
        $this->expectException(Exception::class); //phpcs:ignore
        new SnapshotConfiguration(0);
    }
}
