<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Stream;

class StreamName
{
    /** @var string $value */
    private $value;

    public function __construct(string $name)
    {
        $this->value = $name;
    }

    public static function fromString(string $name) : StreamName
    {
        return new static($name);
    }

    public function __toString() : string
    {
        return $this->value;
    }
}
