<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Utils\Types;

use InvalidArgumentException;
use function sprintf;

class InvalidPositiveNumber extends InvalidArgumentException
{
    public function __construct(int $value)
    {
        parent::__construct(sprintf('"%d" is not a positive number', $value));
    }
}
