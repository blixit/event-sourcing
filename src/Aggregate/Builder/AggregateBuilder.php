<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Aggregate\Builder;

use Blixit\EventSourcing\Aggregate\AggregateRoot;
use Blixit\EventSourcing\Aggregate\AggregateRootInterface;
use Blixit\EventSourcing\Event\EventAccessor;
use Blixit\EventSourcing\Event\EventInterface;
use Blixit\EventSourcing\Stream\Stream;
use ReflectionClass;
use ReflectionException;

class AggregateBuilder implements AggregateBuilderInterface
{
    /**
     * @param mixed $aggregateId
     *
     * @throws ReflectionException
     */
    public function build(Stream $stream, $aggregateId, string $type) : ?AggregateRootInterface
    {
//        $eventAccessor = EventAccessor::getInstance();
//
//        $aggregateClass = '';
//
//        /** @var AggregateRootInterface $aggregate */
//        $aggregate = (new ReflectionClass($aggregateClass))->newInstance();
//
//        foreach ($stream as $event) {
//            /** @var EventInterface $event */
//            $eventAggregateId = $eventAccessor->getAggregateId($event);
//
//            if ($eventAggregateId !== $aggregateId) {
//                continue;
//            }
//
//            $aggregate->apply($event);
//        }
//
//        return $aggregate;
        return null;
    }
}
