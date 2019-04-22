<?php

declare(strict_types=1);

namespace Blixit\EventSourcingTests\Utils;

use PHPUnit\Framework\TestCase;

class AccessorTest extends TestCase
{
    /** @var int $value */
    private $value = 15;

    public function testReadProperty() : void
    {
        /** @var FakeAccessor $accessor */
        $accessor = FakeAccessor::getInstance();

        $value = $accessor->get($this);
        $this->assertSame($this->value, $value);
    }
}
