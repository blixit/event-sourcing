<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Store\Matcher;

interface EventMatcherInterface extends MatcherInterface //phpcs:ignore
{
    public function getStreamName() : ?string;

    /**
     * @return mixed
     */
    public function getAggregateId();

    public function getAggregateClass() : ?string;

    public function getTimestamp() : ?int;

    public function getSequence() : ?int;
}
