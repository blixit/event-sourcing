<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Tests\EventStore\InMemory;

use Blixit\EventSourcing\Store\Exception\CorruptedReadEvent;
use Blixit\EventSourcing\Store\Exception\NonWritableEvent;
use Blixit\EventSourcing\Store\InMemory\InMemoryEventPersister;
use Blixit\EventSourcing\Store\InMemory\InMemoryEventStore;
use Blixit\EventSourcing\Store\Persistence\EventPersisterException;
use Blixit\EventSourcing\Stream\Strategy\OneStreamPerAggregateStrategy;
use Blixit\EventSourcing\Tests\Aggregate\FakeAggregateRoot;
use Blixit\EventSourcing\Tests\Event\FakeEvent;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class InMemoryEventStoreTest extends TestCase
{
    /**
     * @throws CorruptedReadEvent
     * @throws EventPersisterException
     * @throws NonWritableEvent
     * @throws ReflectionException
     */
    public function testInMemoryStore() : void
    {
        $store = new InMemoryEventStore(
            FakeAggregateRoot::class,
            new InMemoryEventPersister(),
            OneStreamPerAggregateStrategy::class
        );

        $aggregate = new FakeAggregateRoot();
        $aggregate->record(FakeEvent::occur('123456789', [
            'data' => 'ok',
            'test' => 'lorem ipsum',
        ]));
        $store->store($aggregate);

        $aggregate2 = $store->get('123456789');
        $this->assertSame($aggregate2->getAggregateId(), '123456789');
    }
}
