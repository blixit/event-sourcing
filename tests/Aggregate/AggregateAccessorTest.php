<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Tests\Aggregate;

use Blixit\EventSourcing\Aggregate\AggregateAccessor;
use PHPUnit\Framework\TestCase;

class AggregateAccessorTest extends TestCase
{
    public function testAggregateIdAccessor() : void
    {
        $expectedAggregateId = 15;
        /** @var AggregateAccessor $aAccessor */
        $aAccessor = AggregateAccessor::getInstance();
        $aggregate = new FakeAggregateRoot($expectedAggregateId);

        $aggregateId = $aggregate->getAggregateId();

        $this->assertTrue($aggregateId === $expectedAggregateId);

        $this->assertSame($expectedAggregateId, $aggregateId);
    }
}
