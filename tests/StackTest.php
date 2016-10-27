<?php

namespace tests;

use PeacefulBit\Packet\Context\Stack;
use PeacefulBit\Packet\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;

class StackTest extends TestCase
{
    public function testInstance()
    {
        $stack = new Stack();

        $this->assertInstanceOf(Stack::class, $stack);
    }

    public function testPushShift()
    {
        $stack = new Stack();

        $stack->push('foo');
        $stack->push('bar');

        $this->assertEquals('bar', $stack->shift());
        $this->assertEquals('foo', $stack->shift());

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Stack is empty');
        $stack->shift();
    }

    public function testGroupShift()
    {
        $stack = new Stack();

        $stack->push('first');

        $stack->push('foo', 'bar', 'baz');

        list ($foo, $bar, $baz) = $stack->shiftGroup(3);

        $this->assertEquals('foo', $foo);
        $this->assertEquals('bar', $bar);
        $this->assertEquals('baz', $baz);

        $this->assertEquals('first', $stack->shift());

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Stack is empty');
        $stack->shift();
    }

    public function testApply()
    {
        $stack = new Stack();

        $sum = function ($a, $b) {
            return $a + $b;
        };

        $result = $stack->push(15, 20)->apply($sum, 2)->shift();

        $this->assertEquals(35, $result);
    }

    public function testRecursiveCall()
    {
        $progression = function ($i) use (&$progression) {
            return $i < 1 ? 0 : $i + $progression($i - 1);
        };
        $this->assertEquals(20100, $progression(200));
    }
}
