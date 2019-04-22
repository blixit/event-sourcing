<?php

declare(strict_types=1);

namespace Blixit\EventSourcingTests\Event;

use Blixit\EventSourcing\Event\Event;
use Blixit\EventSourcing\Event\EventAccessor;
use PHPUnit\Framework\TestCase;

class EventAccessorTest extends TestCase
{
    public function testAccessor() : void
    {
        $e = Event::occur('', $expectedPayload = ['e' => 15]);

        /** @var EventAccessor $eAccessor */
        $eAccessor = EventAccessor::getInstance();

        $eAccessor->setStreamName($e, 'stream');
        $eAccessor->setAggregateClass($e, 'stream');
        $eAccessor->setSequence($e, 1);

        $this->assertSame($expectedPayload, $e->getPayload());
        $this->assertSame('stream', $e->getStreamName());
        $this->assertSame('stream', $e->getAggregateClass());
        $this->assertSame(1, $e->getSequence());
    }
}
