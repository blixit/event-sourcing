<?php

declare(strict_types=1);

namespace Blixit\EventSourcingTests\Stream;

use Blixit\EventSourcing\Event\Event;
use Blixit\EventSourcing\Stream\Stream;
use Blixit\EventSourcing\Stream\StreamName;
use Blixit\EventSourcing\Stream\StreamNotOrderedFailure;
use PHPUnit\Framework\TestCase;

class StreamTest extends TestCase
{
    /**
     * @throws StreamNotOrderedFailure
     */
    public function testStream() : void
    {
        $stream = new Stream(new StreamName($expectedStreamName = 'mystream'));
        $this->assertSame($expectedStreamName, (string) $stream->getStreamName());

        $stream->setStreamName(StreamName::fromString($expectedStreamName = 'newName'));
        $this->assertSame($expectedStreamName, (string) $stream->getStreamName());

        $stream->enqueue(Event::occur('', []));
        $stream->dequeue();

        $this->assertCount(0, $stream);
    }

    /**
     * @throws StreamNotOrderedFailure
     */
    public function testStreamNotOrderedFailure() : void
    {
        // à tester dans le builder TODO:
//        $stream = new Stream(new StreamName($expectedStreamName = 'mystream'));
//
//        $event1 = Event::occur('', []);
//        $event2 = Event::occur('', []);
//
//        $this->expectException(StreamNotOrderedFailure::class);
//        // let insert event2 before event1
//        $stream->enqueue($event2);
//        $stream->enqueue($event1);
    }
}
