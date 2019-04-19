<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Aggregate\Builder;

use Blixit\EventSourcing\Aggregate\AggregateRoot;
use Blixit\EventSourcing\Aggregate\AggregateRootInterface;
use Blixit\EventSourcing\Event\EventAccessor;
use Blixit\EventSourcing\Event\EventInterface;
use Blixit\EventSourcing\Stream\Stream;

class AggregateBuilder implements AggregateBuilderInterface
{
    /**
     * @param mixed $aggregateId
     */
    public function build(Stream $stream, $aggregateId, string $type) : AggregateRootInterface
    {
        $eventAccessor = new EventAccessor();

        $aggregate = AggregateRoot::getInstance();

        foreach ($stream as $event) {
            /** @var EventInterface $event */
            $eventAggregateId = $eventAccessor->getAggregateId($event);

            if ($eventAggregateId !== $aggregateId) {
                continue;
            }

            $aggregate->apply($event);
        }

        return $aggregate;
    }
}
