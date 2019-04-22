<?php

declare(strict_types=1);

namespace Blixit\EventSourcingTests\Messaging;

use Blixit\EventSourcing\Command\CommandInterface;
use Blixit\EventSourcing\Event\EventInterface;
use Blixit\EventSourcing\Messaging\MessageInterface;

class FakeHandlers
{
    public static $LOGGED   = 0;
    public static $ASYNCER  = 0;
    public static $EHANDLER = 0;
    public static $CHANDLER = 0;

    public static function logger(MessageInterface $fakeMessage1) : void
    {
        self::$LOGGED++;
    }
    public static function asyncer(MessageInterface $fakeMessage1) : void
    {
        self::$ASYNCER++;
    }
    public static function eHandler(EventInterface $fakeMessage1) : void
    {
        self::$EHANDLER++;
    }
    public static function cHandler(CommandInterface $fakeMessage1) : void
    {
        self::$CHANDLER++;
    }
}
