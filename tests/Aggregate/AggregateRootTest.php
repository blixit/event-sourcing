<?php

declare(strict_types=1);

namespace Blixit\EventSourcingTests\Aggregate;

use Blixit\EventSourcingTests\Event\FakeEvent;
use PHPUnit\Framework\TestCase;

class AggregateRootTest extends TestCase
{
    public function testAggregateRoot() : void
    {
        $fake = new FakeAggregateRoot('1');
        $fake->record(FakeEvent::occur('1', []));

        $events = $fake->getRecordedEvents();
        $this->assertNotEmpty($events);

        $this->assertInstanceOf(FakeEvent::class, $events[0]);
    }
}
