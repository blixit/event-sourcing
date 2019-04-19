<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Tests\EventStore;

use Blixit\EventSourcing\Aggregate\AggregateAccessor;
use Blixit\EventSourcing\Event\EventAccessor;
use Blixit\EventSourcing\EventStore\EventStore;
use Blixit\EventSourcing\EventStore\Persistence\EventPersisterException;
use Blixit\EventSourcing\Stream\Strategy\OneStreamPerAggregateStrategy;
use Blixit\EventSourcing\Stream\Strategy\SingleAggregateStreamStrategy;
use Blixit\EventSourcing\Stream\Strategy\StreamStrategy;
use Blixit\EventSourcing\Stream\Strategy\UniqueStreamStrategy;
use Blixit\EventSourcing\Tests\Aggregate\FakeAggregateRoot;
use Blixit\EventSourcing\Tests\Event\FakeEvent;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class EventStoreTest extends TestCase
{
    public function testStrategyName() : void
    {
        /** @var AggregateAccessor $aggregateAccessor */
        $aggregateAccessor = AggregateAccessor::getInstance();

        // I. UniqueStreamStrategy
        $eventStore = new EventStore(
            FakeAggregateRoot::class,
            new FakeEventPersister(),
            UniqueStreamStrategy::class
        );

        // check stream strategy name
        $streamName = $eventStore->getStreamNameForAggregateId();
        $this->assertSame((string) $streamName, StreamStrategy::DEFAULT_NAME);

        // II. OneStreamPerAggregateStrategy
        $eventStore = new EventStore(
            FakeAggregateRoot::class,
            new FakeEventPersister(),
            OneStreamPerAggregateStrategy::class
        );

        $aggregate = new FakeAggregateRoot();
        $aggregateAccessor->setAggregateId($aggregate, '123');

        // check stream strategy name
        $streamName = $eventStore->getStreamNameForAggregateId($aggregate->getAggregateId());
        $this->assertSame(
            (string) $streamName,
            FakeAggregateRoot::class . '.' . $aggregate->getAggregateId()
        );

        // III. SingleAggregateStreamStrategy
        $eventStore = new EventStore(
            FakeAggregateRoot::class,
            new FakeEventPersister(),
            SingleAggregateStreamStrategy::class
        );

        // check stream strategy name
        $streamName = $eventStore->getStreamNameForAggregateId();
        $this->assertSame((string) $streamName, FakeAggregateRoot::class);
    }

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
            ->method('getByStream')
            ->willReturn([$event]);
        $eventPersister->expects($this->once())
            ->method('persist')
            ->willReturn($committed);

        /** @var FakeEventPersister $eventPersister */
        $eventStore = new EventStore(
            FakeAggregateRoot::class,
            $eventPersister,
            UniqueStreamStrategy::class
        );
        $aggregate  = $eventStore->get('123');
        $this->assertNotEmpty($aggregate);
        // check that aggregateId is rebuilt
        $this->assertSame($event->getAggregateId(), '123');

        // check sequence
        $initialSequence = $aggregate->getSequence();
        $aggregate->record(FakeEvent::occur('123', []));
        $eventStore->store($aggregate);

        $this->assertEmpty($aggregate->getRecordedEvents());
        $this->assertSame($initialSequence + 1, $aggregate->getSequence());
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
