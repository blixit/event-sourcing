<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Stream;

use Blixit\EventSourcing\Event\EventAccessor;
use Blixit\EventSourcing\Event\EventInterface;
use Countable;
use SplQueue;

class Stream implements Countable
{
    /** @var StreamName $streamName */
    private $streamName;

    /** @var SplQueue $queue */
    private $queue;

    /** @var int $lastEnqueuedSequenceNumber */
    private $lastEnqueuedSequenceNumber;

    /**
     * Event accessor doesnt require to create many instances if
     * many instances of stream are created. The same accessor will be used.
     *
     * @var EventAccessor $eventAccessor
     */
    private static $eventAccessor;

    /**
     * @param EventInterface[] $events
     */
    public function __construct(
        StreamName $streamName,
        ?array $events = [],
        ?callable $beforeEnqueue = null
    ) {
        if (empty(self::$eventAccessor)) {
            // only one item of the event accessor should be instantiated
            self::$eventAccessor = EventAccessor::getInstance();
        }

        $this->queue = new SplQueue();

        if (empty($beforeEnqueue)) {
            $beforeEnqueue = static function (EventInterface $event) : void {}; // phpcs:ignore
        }

        foreach ($events as $event) {
            $beforeEnqueue($event);
            self::$eventAccessor->setStreamName($event, (string) $streamName);
            $this->enqueue($event);
        }

        $this->streamName = $streamName;
    }

    public function getStreamName() : ?StreamName
    {
        return $this->streamName;
    }

    public function setStreamName(StreamName $streamName) : void
    {
        $this->streamName = $streamName;
    }

    public function dequeue() : EventInterface
    {
        return $this->queue->dequeue();
    }

    /**
     * @throws StreamNotOrderedFailure
     */
    public function enqueue(EventInterface $event) : void
    {
        if (! isset($this->lastEnqueuedSequenceNumber)) {
            $this->lastEnqueuedSequenceNumber = $event->getSequence();
        }

        // check sequence order integrity
        if ($event->getSequence() < $this->lastEnqueuedSequenceNumber) {
            throw new StreamNotOrderedFailure($this->streamName);
        }

        $this->queue->enqueue($event);
    }

    /**
     * Count elements of an object
     */
    public function count() : int
    {
        return $this->queue->count();
    }

    public function getIterator() : SplQueue
    {
        return $this->queue;
    }
}
