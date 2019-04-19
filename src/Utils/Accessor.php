<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Utils;

use Closure;

abstract class Accessor
{
    private function __construct()
    {
    }

    public static function getInstance() : self
    {
        $lsbClass = static::class;
        if (empty($lsbClass::$instance)) {
            $lsbClass::$instance = new $lsbClass();
        }
        return $lsbClass::$instance;
    }
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
