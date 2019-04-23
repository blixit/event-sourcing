<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Messaging;

interface DispatcherInterface //phpcs:ignore
{
    public function dispatch(MessageInterface $message) : void;
}
