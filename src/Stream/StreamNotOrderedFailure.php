<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Stream;

use Exception;
use function sprintf;

class StreamNotOrderedFailure extends Exception
{
    public function __construct(StreamName $streamName)
    {
        parent::__construct(sprintf('Stream "%s" was caught with unordered events', $streamName));
    }
}
