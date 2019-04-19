<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Tests\EventStore;

use Blixit\EventSourcing\Event\EventAccessor;
use Blixit\EventSourcing\EventStore\EventStore;
use Blixit\EventSourcing\EventStore\Persistence\EventPersisterException;
use Blixit\EventSourcing\Tests\Aggregate\FakeAggregateRoot;
use Blixit\EventSourcing\Tests\Event\FakeEvent;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class EventStoreTest extends TestCase
{
    /**
     * @throws EventPersisterException;
     * @throws ReflectionException
     */
    public function testEventStoreRead() : void
    {
        /** @var EventAccessor $evAccessor */
        $evAccessor = EventAccessor::getInstance();

        $event = $committed = FakeEvent::occur('123', []);
        $evAccessor->setSequence($committed, 1);

        $eventPersister = $this->createMock(FakeEventPersister::class);
        $eventPersister->expects($this->once())
            ->method('get')
            ->willReturn([$event]);
        $eventPersister->expects($this->once())
            ->method('persist')
            ->willReturn($committed);

        /** @var FakeEventPersister $eventPersister */
        $eventStore = new EventStore(FakeAggregateRoot::class, $eventPersister);
        $aggregate  = $eventStore->get('123');
        $this->assertNotEmpty($aggregate);
        // check that aggregateId is rebuilt
        $this->assertSame($event->getAggregateId(), '123');

        $aggregate->record(FakeEvent::occur('123', []));
        $eventStore->store($aggregate);

        $this->assertEmpty($aggregate->getRecordedEvents());
    }

//    /**
//     * @throws EventPersisterException;
//     * @throws ReflectionException
//     */
//    public function testEventStoreWrite() : void
//    {
//        $event     = FakeEvent::occur('123', []);
//        $aggregate = new FakeAggregateRoot();
//
//        /** @var FakeEventPersister $eventPersister */
//        $eventPersister = $this->createMock(FakeEventPersister::class)
//            ->method('get')
//            ->willReturn([$event]);
//        $eventPersister->method('store')
//            ->willReturnReference($aggregate);
//
//        $eventStore = new EventStore(FakeAggregateRoot::class, $eventPersister);
//        $aggregate  = $eventStore->get('123');
//        $this->assertNotEmpty($aggregate);
//
//        $aggregate->record(FakeEvent::occur('123', []));
//        $eventStore->store($aggregate);
//
//        $this->assertEmpty($aggregate->getRecordedEvents());
//        $this->assertCount(1, $aggregate->getCommittedEvents());
//    }
}
