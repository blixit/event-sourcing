<?php

declare(strict_types=1);

namespace Blixit\EventSourcingTests\Messaging;

use Blixit\EventSourcing\Event\EventInterface;
use Blixit\EventSourcing\Messaging\EventHandlerMiddleware;
use Blixit\EventSourcing\Messaging\MessageInterface;
use Blixit\EventSourcingTests\Event\FakeEvent;
use PHPUnit\Framework\TestCase;

class EventHandlerMiddlewareTest extends TestCase
{
    public function testSupport() : void
    {
        $counter = 0;

        $eHandlerMiddleware = new EventHandlerMiddleware([
            MessageInterface::class => static function () use (&$counter) : void {
                $counter++;
            },
            FakeMessage2::class => static function () use (&$counter) : void {
                $counter++;
            },
            /**
             * This 3rd handler is not handled since we are already into the event
             * middleware. To match all events use MessageInterface::class
             */
            EventInterface::class => static function () use (&$counter) : void {
                $counter += 10;
            },
        ]);

        // accepts FakeMessage1 or any because of '*'
        $this->assertTrue($eHandlerMiddleware->supports(EventInterface::class));
        $this->assertTrue($eHandlerMiddleware->supports(FakeEvent::class));
        // FakeMessage2 doesnt implement EventInterface even if registered
        $this->assertFalse($eHandlerMiddleware->supports(FakeMessage2::class));
        // FakeMessage1 doesnt implement EventInterface
        $this->assertFalse($eHandlerMiddleware->supports(FakeMessage1::class));
        // MessageInterface is the mother interface of EventInterface
        $this->assertFalse($eHandlerMiddleware->supports(MessageInterface::class));

        // ignored message, then keep counter
        $eHandlerMiddleware->handle(new FakeMessage1());
        $this->assertSame($counter, 0);
        // supported fakemessage2, then increase the counter
        $eHandlerMiddleware->handle(new FakeMessage2());
        $this->assertSame($counter, 0);
        // supported command message, then increase the counter
        $eHandlerMiddleware->handle(FakeEvent::occur('', []));
        $this->assertSame($counter, 1);
    }
}
