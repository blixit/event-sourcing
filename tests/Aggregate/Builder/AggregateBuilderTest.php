<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Tests\Aggregate\Builder;

use Blixit\EventSourcing\Aggregate\AggregateRootInterface;
use Blixit\EventSourcing\Aggregate\Builder\AggregateBuilder;
use Blixit\EventSourcing\Stream\Stream;
use Blixit\EventSourcing\Stream\StreamName;
use PHPUnit\Framework\TestCase;

class AggregateBuilderTest extends TestCase
{
    public function testBuilder() : void
    {
        $stream = new Stream(StreamName::fromString('mystream'));

        $builder = new AggregateBuilder();

        $aggregate = $builder->build($stream, 'aggregateId', '');
        $this->assertInstanceOf(AggregateRootInterface::class, $aggregate);
    }
}
