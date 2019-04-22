<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Store\Matcher;

/**
 * Class Filter. A value object that store a filter representation.
 *
 * @link     http://github.com/blixit
 */
class Filter
{
    /** @var string $field */
    private $field;

    /** @var mixed $value */
    private $value;

    /** @var bool $isActive */
    private $isActive;

    /**
     * @param mixed $searchedValue
     */
    public function __construct(string $field, $searchedValue, bool $isActive = true)
    {
        $this->field    = $field;
        $this->value    = $searchedValue;
        $this->isActive = $isActive;
    }

    public function getField() : string
    {
        return $this->field;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    public function isActive() : bool
    {
        return $this->isActive;
    }
}
