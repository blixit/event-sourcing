<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Messaging;

class Dispatcher
{
    /** @var MessagingMiddlewareInterface[] $middlewares */
    protected $middlewares;
    /**
     * @param MessagingMiddlewareInterface[] $middlewares
     */
    public function __construct(array $middlewares)
    {
        $this->middlewares = $middlewares;
    }

    public function dispatch(MessageInterface $message) : void
    {
        foreach ($this->middlewares as $middleware) {
            $middleware->handle($message);
        }
    }
}
