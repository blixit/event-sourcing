<?php

declare(strict_types=1);

namespace Blixit\EventSourcingTests\Utils;

use Blixit\EventSourcing\Utils\Accessor;

class FakeAccessor extends Accessor
{
    /** @var Accessor $instance */
    protected static $instance;

    public function get(AccessorTest $object) : int
    {
        return $this->readProperty($object, 'value');
    }

    /**
     * @param mixed $object
     *
     * @return mixed
     */
    public function getConfiguration($object)
    {
        return $this->readProperty($object, 'configuration');
    }
}
