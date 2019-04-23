<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Store\Matcher;

class EventMatcher extends Matcher implements EventMatcherInterface
{
    /** @var string[] $allowedFields */
    protected $allowedFields = ['streamName', 'aggregateId', 'aggregateClass', 'timestamp', 'sequence'];

    public function getStreamName() : ?string
    {
        return isset($this->fields['streamName']) ? $this->fields['streamName']->getValue() : null;
    }

    /**
     * @return mixed
     */
    public function getAggregateId()
    {
        return isset($this->fields['aggregateId']) ? $this->fields['aggregateId']->getValue() : null;
    }

    public function getAggregateClass() : ?string
    {
        return isset($this->fields['aggregateClass']) ? $this->fields['aggregateClass']->getValue() : null;
    }

    public function getTimestamp() : ?int
    {
        return isset($this->fields['timestamp']) ? $this->fields['timestamp']->getValue() : null;
    }

    public function getSequence() : ?int
    {
        return isset($this->fields['sequence']) ? $this->fields['sequence']->getValue() : null;
    }
}
