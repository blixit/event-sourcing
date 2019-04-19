<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Tests\Event;

use Blixit\EventSourcing\Event\DataStructure\Payload;
use Blixit\EventSourcing\Event\Event;
use Blixit\EventSourcing\Event\EventAccessor;
use PHPUnit\Framework\TestCase;
use function date;
use function strtotime;

class EventAccessorTest extends TestCase
{
    public function testPayloadAccessor() : void
    {
        $e = Event::occur('', $expectedPayload = ['e' => 15]);

        $eAccessor = new EventAccessor();

        $payload = $eAccessor->getPayload($e);

        $this->assertInstanceOf(Payload::class, $payload);

        $this->assertSame($expectedPayload, $payload->getArrayCopy());
    }

    public function testAggregateIdAccessor() : void
    {
        $e = Event::occur($expectedAggregateId = '', []);

        $eAccessor = new EventAccessor();

        $aggregateId = $eAccessor->getAggregateId($e);

        $this->assertTrue($aggregateId === $expectedAggregateId);

        $this->assertSame($expectedAggregateId, $aggregateId);
    }

    public function testSequenceAccessor() : void
    {
        $e = Event::occur($expectedAggregateId = '', []);

        $eAccessor = new EventAccessor();

        $sequence = $eAccessor->getSequence($e);

        $this->assertIsNumeric($sequence);
        $this->assertSame(0, $sequence);
    }
}
