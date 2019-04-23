<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Store\Matcher;

/**
 * Class Filter. A value object that store a filter representation.
 *
 * @link     http://github.com/blixit
 */
class FilterObject
{
    /** @var string $field */
    private $field;

    /** @var mixed $value */
    private $value;

    /** @var bool $isActive */
    private $isActive;

    /**
     * @param mixed $value
     */
    public function __construct(string $field, $value, bool $isActive = true)
    {
        $this->field    = $field;
        $this->value    = $value;
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
