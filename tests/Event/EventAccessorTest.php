<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Tests\Event;

use Blixit\EventSourcing\Event\DataStructure\Payload;
use Blixit\EventSourcing\Event\Event;
use Blixit\EventSourcing\Event\EventAccessor;
use PHPUnit\Framework\TestCase;

class EventAccessorTest extends TestCase
{
    public function testPayloadAccessor() : void
    {
        $e = Event::occur('', $expectedPayload = ['e' => 15]);

        /** @var EventAccessor $eAccessor */
        $eAccessor = EventAccessor::getInstance();

        $payload = $eAccessor->getRealPayload($e);

        $this->assertInstanceOf(Payload::class, $payload);

        $this->assertSame($expectedPayload, $payload->getArrayCopy());
    }
}
