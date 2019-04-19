<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Utils;

use Closure;

class Accessor
{
    /**
     * @param mixed $object
     *
     * @return mixed
     */
    protected function readProperty($object, string $property)
    {
        return Closure::bind(static function ($object) use ($property) {
            return $object->$property;
        }, null, $object)($object);
    }

    /**
     * @param mixed $object
     * @param mixed $value
     */
    protected function writeProperty(&$object, string $property, $value) : void
    {
        Closure::bind(static function (&$object) use ($property, $value) : void {
            $object->$property = $value;
        }, null, $object)($object);
    }
}
