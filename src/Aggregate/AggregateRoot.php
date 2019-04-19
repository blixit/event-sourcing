<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Aggregate;

use Blixit\EventSourcing\Event\EventInterface;

class AggregateRoot implements AggregateRootInterface
{
    public const DEFAULT_SEQUENCE_POSITION = 1;

    /** @var mixed $aggregateId */
    private $aggregateId;

    /** @var int $versionSequence */
    private $versionSequence = self::DEFAULT_SEQUENCE_POSITION;

    public static function getInstance() : AggregateRootInterface
    {
        return new static();
    }

    /**
     * @return mixed
     */
    public function getAggregateId()
    {
        return $this->aggregateId;
    }

    public function apply(EventInterface $event) : void
    {
    }
}
