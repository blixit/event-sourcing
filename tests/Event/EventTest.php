<?php

declare(strict_types=1);

namespace Blixit\EventSourcingTests\Event;

use Blixit\EventSourcing\Event\Event;
use Blixit\EventSourcing\Event\EventInterface;
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
    public function testEvent() : void
    {
        $e = Event::occur('', []);
        $this->assertInstanceOf(EventInterface::class, $e);

        $this->assertSame([], $e->getPayload());
    }
}
