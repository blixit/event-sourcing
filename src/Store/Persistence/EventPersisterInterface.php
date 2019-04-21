<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Store\Persistence;

interface EventPersisterInterface extends EventReaderInterface, EventWriterInterface //phpcs:ignore
{
}
