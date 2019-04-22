<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Messaging;

class MessageBrokerMiddleWare extends MessagingMiddleware
{
    public function supports(string $class) : bool
    {
        return true;
    }
}
