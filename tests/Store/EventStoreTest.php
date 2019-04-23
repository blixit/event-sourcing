<?php

declare(strict_types=1);

namespace Blixit\EventSourcingTests\Store;

use Blixit\EventSourcing\Aggregate\AggregateAccessor;
use Blixit\EventSourcing\Event\EventAccessor;
use Blixit\EventSourcing\Messaging\CommandHandlerMiddleware;
use Blixit\EventSourcing\Messaging\Dispatcher;
use Blixit\EventSourcing\Messaging\EventHandlerMiddleware;
use Blixit\EventSourcing\Messaging\LoggingMiddleware;
use Blixit\EventSourcing\Messaging\MessageBrokerMiddleWare;
use Blixit\EventSourcing\Messaging\MessageInterface;
use Blixit\EventSourcing\Store\EventStore;
use Blixit\EventSourcing\Store\Exception\CorruptedReadEvent;
use Blixit\EventSourcing\Store\Exception\NonWritableEvent;
use Blixit\EventSourcing\Store\InMemory\InMemoryEventPersister;
use Blixit\EventSourcing\Store\Persistence\EventPersisterException;
use Blixit\EventSourcing\Stream\Strategy\OneStreamPerAggregateStrategy;
use Blixit\EventSourcing\Stream\Strategy\SingleAggregateStreamStrategy;
use Blixit\EventSourcing\Stream\Strategy\StreamStrategy;
use Blixit\EventSourcing\Stream\Strategy\UniqueStreamStrategy;
use Blixit\EventSourcingTests\Aggregate\FakeAggregateRoot;
use Blixit\EventSourcingTests\Command\FakeCommand;
use Blixit\EventSourcingTests\Event\FakeEvent;
use Blixit\EventSourcingTests\Messaging\FakeHandlers;
use Blixit\EventSourcingTests\Messaging\FakeMessage1;
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

        $aggregate = new FakeAggregateRoot('123');

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
     * @throws CorruptedReadEvent
     * @throws NonWritableEvent
     */
    public function testEventStoreRead() : void
    {
        /** @var EventAccessor $evAccessor */
        $evAccessor = EventAccessor::getInstance();

        $event = $committed = FakeEvent::occur('123', []);
        $evAccessor->setSequence($committed, 1);
        $evAccessor->setAggregateClass($committed, FakeAggregateRoot::class);
        $evAccessor->setStreamName($committed, StreamStrategy::DEFAULT_NAME);

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
        $this->assertSame($aggregate->getAggregateId(), '123');

        // check sequence
        $initialSequence = $aggregate->getSequence();
        $aggregate->record(FakeEvent::occur('123', []));
        $eventStore->store($aggregate);

        $this->assertEmpty($aggregate->getRecordedEvents());
        $this->assertSame($initialSequence + 1, $aggregate->getSequence());
    }

    /**
     * @throws EventPersisterException;
     * @throws ReflectionException
     */
    public function testEventStoreDispatchingEvents() : void
    {
        /** @var Dispatcher $dispatcher */
        $dispatcher = $this->createMock(Dispatcher::class);
        $dispatcher->expects($this->once())
            ->method('dispatch');
        /** @var FakeEventPersister $eventPersister */
        $eventStore = new EventStore(
            FakeAggregateRoot::class,
            new InMemoryEventPersister(),
            UniqueStreamStrategy::class,
            $dispatcher
        );

        $aggregate = new FakeAggregateRoot('123');

        $aggregate->record(FakeEvent::occur('123', []));
        $eventStore->store($aggregate);

        $this->assertEmpty($aggregate->getRecordedEvents());
    }

    /**
     * @throws EventPersisterException;
     * @throws ReflectionException
     */
    public function testHandlingOfEvents() : void
    {
        $dispatcher = new Dispatcher([
            MessageBrokerMiddleWare::class => new MessageBrokerMiddleWare([
                MessageInterface::class => [
                    'rmq' => [FakeHandlers::class, 'asyncer'],
                ],
            ]),
            LoggingMiddleware::class => new LoggingMiddleware([
                MessageInterface::class => [
                    'log' => [FakeHandlers::class, 'logger'],
                ],
            ]),
            EventHandlerMiddleware::class => new EventHandlerMiddleware([
                FakeEvent::class => [
                    'eHandler' => [FakeHandlers::class, 'eHandler'],
                    'mailer' => [FakeHandlers::class, 'eHandler'],
                ],
            ]),
            CommandHandlerMiddleware::class => new CommandHandlerMiddleware([
                FakeMessage1::class => [
                    'cHandler' => [FakeHandlers::class, 'cHandler'],
                ],
                FakeCommand::class => [FakeHandlers::class, 'cHandler'],
            ]),
        ]);

        /** @var FakeEventPersister $eventPersister */
        $eventStore = new EventStore(
            FakeAggregateRoot::class,
            new InMemoryEventPersister(),
            UniqueStreamStrategy::class,
            $dispatcher
        );

        $aggregate = new FakeAggregateRoot('123');

        $aggregate->record(FakeEvent::occur('123', []));
        FakeHandlers::$EHANDLER = 0;
        $eventStore->store($aggregate);

        $this->assertEmpty($aggregate->getRecordedEvents());
        $this->assertSame(2, FakeHandlers::$EHANDLER);
    }
}
