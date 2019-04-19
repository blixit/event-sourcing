<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Tests\Aggregate;

use Blixit\EventSourcing\Aggregate\AggregateAccessor;
use PHPUnit\Framework\TestCase;

class AggregateAccessorTest extends TestCase
{
    public function testAggregateIdAccessor() : void
    {
        $aggregate = new FakeAggregateRoot();
        $aAccessor = AggregateAccessor::getInstance();

        $expectedAggregateId = 15;
        $aAccessor->setAggregateId($aggregate, $expectedAggregateId);
        $aggregateId = $aAccessor->getAggregateId($aggregate);

        $this->assertTrue($aggregateId === $expectedAggregateId);

        $this->assertSame($expectedAggregateId, $aggregateId);
    }
}
