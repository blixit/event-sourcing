<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Utils;

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
        return (function ($property) {
            return $this->$property;
        })->bindTo($object)->call($object, $property);
    }

    /**
     * @param mixed $object
     * @param mixed $value
     */
    protected function writeProperty(&$object, string $property, $value) : void
    {
        (function ($property, $value) : void {
            $this->$property = $value;
        })->bindTo($object)->call($object, $property, $value);
    }
}
