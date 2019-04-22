<?php

declare(strict_types=1);

namespace Blixit\EventSourcingTests\Messaging;

use Blixit\EventSourcing\Messaging\CommandHandlerMiddleware;
use Blixit\EventSourcing\Messaging\Dispatcher;
use Blixit\EventSourcing\Messaging\EventHandlerMiddleware;
use Blixit\EventSourcing\Messaging\LoggingMiddleware;
use Blixit\EventSourcing\Messaging\MessageBrokerMiddleWare;
use Blixit\EventSourcing\Messaging\MessageInterface;
use Blixit\EventSourcingTests\Command\FakeCommand;
use Blixit\EventSourcingTests\Event\FakeEvent;
use PHPUnit\Framework\TestCase;

class DispatcherTest extends TestCase
{
    public function testDispatcher() : void
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

        $dispatcher->dispatch(new FakeMessage1());
        $dispatcher->dispatch(new FakeCommand());
        $dispatcher->dispatch(FakeEvent::occur('', []));
        $this->assertSame(FakeHandlers::$LOGGED, 3);
        $this->assertSame(FakeHandlers::$ASYNCER, 3);
        $this->assertSame(FakeHandlers::$EHANDLER, 2);
        $this->assertSame(FakeHandlers::$CHANDLER, 1);
    }
}
