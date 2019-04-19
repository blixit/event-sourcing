<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\EventStore\Persistence;

interface EventPersisterInterface extends EventReaderInterface, EventWriterInterface //phpcs:ignore
{
}
