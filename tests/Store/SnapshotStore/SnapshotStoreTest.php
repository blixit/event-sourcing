<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Tests\Store\SnapshotStore;

use Blixit\EventSourcing\Store\InMemory\InMemoryEventPersister;
use Blixit\EventSourcing\Store\InMemory\InMemorySnapshotPersister;
use Blixit\EventSourcing\Store\SnapshotStore\SnapshotConfiguration;
use Blixit\EventSourcing\Store\SnapshotStore\SnapshotStore;
use Blixit\EventSourcing\Stream\Strategy\UniqueStreamStrategy;
use Blixit\EventSourcing\Tests\Aggregate\FakeAggregateRoot;
use Blixit\EventSourcing\Tests\Event\FakeEvent;
use Exception;
use PHPUnit\Framework\TestCase;

class SnapshotStoreTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testToSnapshot() : void
    {
        $aggregate = new FakeAggregateRoot('123');

        $snapshotStore = new SnapshotStore(
            FakeAggregateRoot::class,
            new InMemoryEventPersister(),
            UniqueStreamStrategy::class,
            new InMemorySnapshotPersister(),
            new SnapshotConfiguration(2),
            null
        );

        $aggregate->record(FakeEvent::occur('123', ['increment' => 1]));
        $aggregate->record(FakeEvent::occur('123', ['increment' => 3]));
        $aggregate->record(FakeEvent::occur('123', ['increment' => 8]));
        $snapshotStore->store($aggregate);
        /** @var FakeAggregateRoot $aggregate */
        $aggregate = $snapshotStore->get('123');
        $this->assertSame($aggregate->getSequence(), 3);
        $this->assertSame($aggregate->getProperty(), 1 + 3 + 8);
    }

    /**
     * @throws Exception
     */
    public function testToSnapshotStepsGreaterThanEvents() : void
    {
        $steps     = 10; // greater than 10
        $aggregate = new FakeAggregateRoot('123');

        $snapshotStore = new SnapshotStore(
            FakeAggregateRoot::class,
            new InMemoryEventPersister(),
            UniqueStreamStrategy::class,
            new InMemorySnapshotPersister(),
            new SnapshotConfiguration($steps),
            null
        );

        $aggregate->record(FakeEvent::occur('123', ['increment' => 1]));
        $aggregate->record(FakeEvent::occur('123', ['increment' => 3]));
        $aggregate->record(FakeEvent::occur('123', ['increment' => 8]));
        $snapshotStore->store($aggregate);
        /** @var FakeAggregateRoot $aggregate */
        $aggregate = $snapshotStore->get('123');
        $this->assertSame($aggregate->getSequence(), 3);
        $this->assertSame($aggregate->getProperty(), 1 + 3 + 8);
    }
}
