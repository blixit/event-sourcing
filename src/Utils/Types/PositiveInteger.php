<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Utils\Types;

class PositiveInteger
{
    /** @var int $value */
    private $value;

    private function __construct(int $value)
    {
        if ($value < 0) {
            throw new InvalidPositiveNumber($value);
        }
        $this->value = $value;
    }

    public static function fromInt(int $value) : PositiveInteger
    {
        return new static($value);
    }

    public function getValue() : int
    {
        return $this->value;
    }
}
