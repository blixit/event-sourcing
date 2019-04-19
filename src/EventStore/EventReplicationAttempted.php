<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\EventStore;

use Blixit\EventSourcing\Event\EventInterface;
use LogicException;
use ReflectionObject;
use function sprintf;

class EventReplicationAttempted extends LogicException
{
    public function __construct(EventInterface $event)
    {
        $reflector = new ReflectionObject($event);
        parent::__construct(sprintf(
            'An event cannot be store twice: %s (%s)',
            $reflector->getName(),
            $event->getAggregateId()
        ));
    }
}
