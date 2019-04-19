<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Aggregate\Replay;

use Blixit\EventSourcing\Aggregate\AggregateAccessor;
use Blixit\EventSourcing\Aggregate\AggregateRoot;
use Blixit\EventSourcing\Aggregate\AggregateRootInterface;
use Blixit\EventSourcing\Event\EventAccessor;
use Blixit\EventSourcing\Event\EventInterface;
use Blixit\EventSourcing\Stream\Stream;
use Blixit\EventSourcing\Utils\Types\PositiveInteger;
use ReflectionClass;
use ReflectionException;
use function get_class;

class EventPlayer implements EventPlayerInterface
{
    /**
     * @param mixed $aggregateId
     *
     * @throws ReflectionException
     */
    public function replay(
        Stream $stream,
        string $aggregateClass,
        $aggregateId,
        ?int $initialPosition = 0,
        ?string $eventType = null
    ) : ?AggregateRootInterface {
        /** @var AggregateRootInterface $aggregate */
        $aggregate = (new ReflectionClass($aggregateClass))->newInstance();
        return $this->replayFromAggregate($stream, $aggregate, $aggregateId, $initialPosition, $eventType);
    }

    /**
     * @param mixed $aggregateId
     */
    public function replayFromAggregate(
        Stream $stream,
        AggregateRootInterface $aggregate,
        $aggregateId,
        ?int $initialPosition = 0,
        ?string $eventType = null
    ) : ?AggregateRootInterface {
        $eventAccessor     = EventAccessor::getInstance();
        $aggregateAccessor = AggregateAccessor::getInstance();

        $starterPosition = PositiveInteger::fromInt($initialPosition);

//        $handlers = $this->resolveHandlers($eventType);
        $versionSequence = $aggregateAccessor->getVersionSequence($aggregate);

        $eventsFound = 0;
        foreach ($stream->getIterator() as $i => $event) {
            // ignores not relevant events
            if ($i < $starterPosition->getValue()) {
                continue;
            }
            /** @var EventInterface $event */
            $eventAggregateId = $eventAccessor->getAggregateId($event);

            // ignores not relevant events
            if ($eventAggregateId !== $aggregateId) {
                continue;
            }

            $eventClassName = get_class($event);

            // ignore event with bad type
            if (isset($eventType) && $eventType !== $eventClassName) {
                continue;
            }

            $eventsFound++;

            // set default aggregateId with the first event that matches the search conditions
            // and the sequence is empty. ==> the aggregate is being built
            if (empty($aggregate->getAggregateId()) && $versionSequence === AggregateRoot::DEFAULT_SEQUENCE_POSITION) {
                $aggregateAccessor->setAggregateId($aggregate, $eventAggregateId);
            }

            $aggregate->apply($event);
        }

        return $eventsFound > 0 ? $aggregate : null;
    }
}
