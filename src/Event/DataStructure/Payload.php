<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Event\DataStructure;

use ArrayObject;

class Payload extends ArrayObject
{
    /**
     * @param mixed[] $value
     */
    public static function fromArray(array $value) : Payload
    {
        return new static($value);
    }
}
