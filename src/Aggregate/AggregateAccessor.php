<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Aggregate;

use Blixit\EventSourcing\Utils\Accessor;

class AggregateAccessor extends Accessor
{
    /** @var AggregateAccessor $instance */
    protected static $instance;

    /**
     * @param mixed $value
     */
    public function setAggregateId(AggregateRootInterface &$aggregateRoot, $value) : void
    {
        $this->writeProperty($aggregateRoot, 'aggregateId', $value);
    }

    /**
     * @return mixed
     */
    public function getAggregateId(AggregateRootInterface $aggregateRoot)
    {
        return $this->readProperty($aggregateRoot, 'aggregateId');
    }

    public function getVersionSequence(AggregateRootInterface $aggregateRoot) : int
    {
        return $this->readProperty($aggregateRoot, 'versionSequence');
    }
}
