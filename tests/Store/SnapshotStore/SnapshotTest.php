<?php

declare(strict_types=1);

namespace Blixit\EventSourcingTests\Store\SnapshotStore;

use Blixit\EventSourcing\Store\SnapshotStore\Snapshot;
use PHPUnit\Framework\TestCase;

class SnapshotTest extends TestCase
{
    public function testSnapshot() : void
    {
        $snapshot = new Snapshot('stream', 'payload');
        $this->assertSame('stream', $snapshot->getStreamName());
        $this->assertSame('payload', $snapshot->getPayload());
    }
}
