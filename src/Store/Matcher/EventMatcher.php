<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Store\Matcher;

class EventMatcher extends Matcher
{
    /** @var string[] $allowedFields */
    protected $allowedFields = ['streamName', 'aggregateId', 'aggregateClass', 'timestamp', 'sequence'];
}
