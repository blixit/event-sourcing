<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Command;

use Blixit\EventSourcing\Messaging\MessageInterface;

interface CommandInterface extends MessageInterface //phpcs:ignore
{
}
