<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Messaging;

use Blixit\EventSourcing\Event\EventInterface;
use ReflectionClass;
use ReflectionException;

class EventHandlerMiddleware extends MessagingMiddleware
{
    /**
     * @throws ReflectionException
     */
    public function supports(string $class) : bool
    {
        return (new ReflectionClass($class))
            ->implementsInterface(EventInterface::class);
    }
}
