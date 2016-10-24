<?php

namespace tests;

use PeacefulBit\Pocket\Calculator;
use PeacefulBit\Pocket\Parser\Nodes\NativeNode;
use PeacefulBit\Pocket\Parser\Visitors\NodeCalculatorVisitor;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    public function testNativeCall()
    {
        $calculator = new Calculator([
            '+' => new NativeNode('+', function (NodeCalculatorVisitor $visitor, array $arguments) {
                return array_reduce($arguments, function ($acc, $argument) use ($visitor) {
                    return $acc + $visitor->visit($argument);
                }, 0);
            })
        ]);

        $result = $calculator->calculate("(+ 1 2)");

        $this->assertEquals(3, $result);
    }

    public function testStringCall()
    {
        $calculator = new Calculator();
        $result = $calculator->calculate('"foo"');
        $this->assertEquals('foo', $result);
    }

    public function testConstantDefine()
    {
        $calculator = new Calculator();
        $result = $calculator->calculate('(def foo "bar") foo');
        $this->assertEquals('bar', $result);
    }

    public function testFunctionDefine()
    {
        $calculator = new Calculator();
        $result = $calculator->calculate('(def (foo) "bar") (foo)');
        $this->assertEquals('bar', $result);
    }

    public function testFunctionAsArgument()
    {
        $calculator = new Calculator();
        $result = $calculator->calculate('(def (foo) "bar") (def (baz) foo) ((baz))');
        $this->assertEquals('bar', $result);
    }
}
