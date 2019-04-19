<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Tests\Aggregate;

use Blixit\EventSourcing\Aggregate\AggregateAccessor;
use Blixit\EventSourcing\Aggregate\AggregateRoot;
use PHPUnit\Framework\TestCase;

class AggregateAccessorTest extends TestCase
{
    public function testAggregateIdAccessor() : void
    {
        $aggregate = AggregateRoot::getInstance();

        $aAccessor = new AggregateAccessor();

        $expectedAggregateId = 15;
        $aAccessor->setAggregateId($aggregate, $expectedAggregateId);
        $aggregateId = $aAccessor->getAggregateId($aggregate);

        $this->assertTrue($aggregateId === $expectedAggregateId);

        $this->assertSame($expectedAggregateId, $aggregateId);
    }
}
