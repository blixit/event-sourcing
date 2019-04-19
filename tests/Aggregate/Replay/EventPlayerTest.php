<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Tests\Aggregate\Replay;

use Blixit\EventSourcing\Aggregate\AggregateRootInterface;
use Blixit\EventSourcing\Aggregate\Replay\EventPlayer;
use Blixit\EventSourcing\Event\Event;
use Blixit\EventSourcing\Stream\Stream;
use Blixit\EventSourcing\Stream\StreamName;
use Blixit\EventSourcing\Stream\StreamNotOrderedFailure;
use Blixit\EventSourcing\Tests\Aggregate\FakeAggregateRoot;
use Blixit\EventSourcing\Tests\Event\FakeEvent;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use function microtime;

class EventPlayerTest extends TestCase
{
    /**
     * @throws StreamNotOrderedFailure
     * @throws ReflectionException
     */
    public function testPlayer() : void
    {
        $player = new EventPlayer();
        $stream = new Stream(StreamName::fromString('mystream'));

        $stream->enqueue(Event::occur('123', []));
        $stream->enqueue(Event::occur('156', []));
        $stream->enqueue(Event::occur('12', []));

        // there is 0 event of this aggregate starting from the position 2
        $aggregate = $player->replay($stream, FakeAggregateRoot::class, '123', 2);
        $this->assertNull($aggregate);

        // there is one event of this aggregate starting from the position 0
        $aggregate = $player->replay($stream, FakeAggregateRoot::class, '123', 0);
        $this->assertInstanceOf(AggregateRootInterface::class, $aggregate);
        $this->assertSame($aggregate->getAggregateId(), '123');
    }

    /**
     * @throws StreamNotOrderedFailure
     * @throws ReflectionException
     */
    public function testTypeHandling() : void
    {
        $player = new EventPlayer();
        $stream = new Stream(StreamName::fromString('mystream'));

        $stream->enqueue(FakeEvent::occur('123', []));
        $stream->enqueue(Event::occur('123', []));

        // there is 0 event of this aggregate and this type (FakeEvent) starting from the position 1
        $aggregate = $player->replay($stream, FakeAggregateRoot::class, '123', 1, FakeEvent::class);
        $this->assertNull($aggregate);

        // there is one event of this aggregate and this type (FakeEvent) starting from the position 0
        $aggregate = $player->replay($stream, FakeAggregateRoot::class, '123', 0, FakeEvent::class);
        $this->assertInstanceOf(AggregateRootInterface::class, $aggregate);
        $this->assertSame($aggregate->getAggregateId(), '123');
    }

    /**
     * @throws ReflectionException
     * @throws StreamNotOrderedFailure
     */
    public function testPerformances() : void
    {
        $player = new EventPlayer();
        $stream = new Stream(StreamName::fromString('mystream'));
        $length = 150000;

        $t = microtime(true);
        for ($i = 0; $i < $length; $i++) {
            $stream->enqueue(FakeEvent::occur('123', []));
        }
        $t = microtime(true) - $t;
        $this->assertLessThan(0.5, $t, $t . ' should be under 0.5s');

        $t = microtime(true);
        $player->replay($stream, FakeAggregateRoot::class, '123', 0, FakeEvent::class);
        $tZero = microtime(true) - $t;
        $this->assertTrue($tZero < .5, $tZero . ' not < 0.5s ');
    }
}
