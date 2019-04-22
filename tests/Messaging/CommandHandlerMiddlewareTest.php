<?php

declare(strict_types=1);

namespace Blixit\EventSourcingTests\Messaging;

use Blixit\EventSourcing\Command\CommandInterface;
use Blixit\EventSourcing\Messaging\CommandHandlerMiddleware;
use Blixit\EventSourcing\Messaging\MessageInterface;
use Blixit\EventSourcingTests\Command\FakeCommand;
use PHPUnit\Framework\TestCase;

class CommandHandlerMiddlewareTest extends TestCase
{
    public function testSupports() : void
    {
        $counter = 0;

        $cHandlerMiddleware = new CommandHandlerMiddleware([
            MessageInterface::class => static function () use (&$counter) : void {
                $counter++;
            },
            FakeMessage2::class => static function () use (&$counter) : void {
                $counter++;
            },
            /**
             * This 3rd handler is not handled since we are already into the command
             * middleware. To match all commands use MessageInterface::class
             */
            CommandInterface::class => static function () use (&$counter) : void {
                $counter += 10;
            },
        ]);

        // accepts FakeMessage1 or any because of '*'
        $this->assertTrue($cHandlerMiddleware->supports(CommandInterface::class));
        $this->assertTrue($cHandlerMiddleware->supports(FakeCommand::class));
        // FakeMessage2 doesnt implement CommandInterface even if registered
        $this->assertFalse($cHandlerMiddleware->supports(FakeMessage2::class));
        // FakeMessage1 doesnt implement CommandInterface
        $this->assertFalse($cHandlerMiddleware->supports(FakeMessage1::class));
        // MessageInterface is the mother interface of CommandInterface
        $this->assertFalse($cHandlerMiddleware->supports(MessageInterface::class));

        // ignored message, then keep counter
        $cHandlerMiddleware->handle(new FakeMessage1());
        $this->assertSame($counter, 0);
        // supported fakemessage2, then increase the counter
        $cHandlerMiddleware->handle(new FakeMessage2());
        $this->assertSame($counter, 0);
        // supported command message, then increase the counter
        $cHandlerMiddleware->handle(new FakeCommand());
        $this->assertSame($counter, 1);
    }
}
