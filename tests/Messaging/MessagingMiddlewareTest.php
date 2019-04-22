<?php

declare(strict_types=1);

namespace Blixit\EventSourcingTests\Messaging;

use Blixit\EventSourcing\Messaging\MessageInterface;
use Blixit\EventSourcing\Messaging\MessagingMiddleware;
use Blixit\EventSourcing\Messaging\MessagingMiddlewareInterface;
use PHPUnit\Framework\TestCase;

class MessagingMiddlewareTest extends TestCase
{
    public function testConstruct() : void
    {
        $middleware = new MessagingMiddleware([
            'supportedClass' => [],
            'supportedClass2' => [],
        ]);
        $this->assertInstanceOf(MessagingMiddleware::class, $middleware);
        $this->assertInstanceOf(MessagingMiddlewareInterface::class, $middleware);
    }

    public function testSupport() : void
    {
        $middleware = new MessagingMiddleware([
            'supportedClass' => [],
            'supportedClass2' => [],
        ]);
        $this->assertTrue($middleware->supports('supportedClass'));
        $this->assertTrue($middleware->supports('supportedClass2'));
    }

    public function testHandle() : void
    {
        $counter    = 0;
        $middleware = new MessagingMiddleware([
            FakeMessage1::class => static function () use (&$counter) : void {
                $counter += 1;
            },
            FakeMessage2::class => static function () use (&$counter) : void {
                $counter = 100;
            },
            MessageInterface::class => static function () use (&$counter) : void {
                $counter += 2000;
            },
        ]);
        $middleware->handle(new FakeMessage1());
        $this->assertSame($counter, 2001);
        $counter = 0;
        $middleware->handle(new FakeMessage2());
        $this->assertSame($counter, 2100);
    }
}
