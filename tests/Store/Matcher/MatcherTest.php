<?php

declare(strict_types=1);

namespace Blixit\EventSourcingTests\Store\Matcher;

use Blixit\EventSourcing\Store\Matcher\EventMatcher;
use Blixit\EventSourcing\Store\Matcher\FilterObject;
use Blixit\EventSourcing\Store\Matcher\Matcher;
use Blixit\EventSourcing\Store\Matcher\MatcherException;
use Blixit\EventSourcing\Store\Matcher\MatcherInterface;
use PHPUnit\Framework\TestCase;

class MatcherTest extends TestCase
{
    public function testConstructor() : void
    {
        $matcher = new Matcher([]);
        $this->assertInstanceOf(MatcherInterface::class, $matcher);

        $matcher = new Matcher([
            new FilterObject('streamname', 'user-1'),
            new FilterObject('sequence', 10),
        ], ['streamname', 'sequence']);
        $this->assertCount(2, $matcher->getFields());

        $this->assertSame('user-1', $matcher->getFields()['streamname']->getValue());
        $this->assertSame(10, $matcher->getFields()['sequence']->getValue());
    }

    public function testSetSearchField() : void
    {
        $matcher = new Matcher([]);
        $this->assertInstanceOf(MatcherInterface::class, $matcher);

        $this->expectException(MatcherException::class);
        $matcher->addSearchField(new FilterObject('test', 'value'));
    }

    public function testAddAllowedField() : void
    {
        $matcher = new Matcher([]);
        $this->assertInstanceOf(MatcherInterface::class, $matcher);

        $matcher->addAllowedField('test');
        $matcher->addSearchField(new FilterObject('test', 'value'));
    }

    public function testEventMatcher() : void
    {
        $eventMatcher = new EventMatcher([
            new FilterObject('streamName', 'user-1'),
        ]);
        $this->expectException(MatcherException::class);
        $eventMatcher->addSearchField(new FilterObject('notAllowed', 25));
    }
}
