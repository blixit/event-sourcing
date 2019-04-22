<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Messaging;

use Blixit\EventSourcing\Command\CommandInterface;
use ReflectionClass;
use ReflectionException;

class CommandHandlerMiddleware extends MessagingMiddleware
{
    /**
     * @throws ReflectionException
     */
    public function supports(string $class) : bool
    {
        return (new ReflectionClass($class))
            ->implementsInterface(CommandInterface::class);
    }
}
