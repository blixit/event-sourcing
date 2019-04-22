<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Messaging;

interface MessagingMiddlewareInterface //phpcs:ignore
{
    public function supports(string $class) : bool;

    public function handle(MessageInterface $message) : void;
}
