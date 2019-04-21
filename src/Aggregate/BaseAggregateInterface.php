<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Aggregate;

interface BaseAggregateInterface // phpcs:ignore
{
    /**
     * @return mixed
     */
    public function getAggregateId();
}
