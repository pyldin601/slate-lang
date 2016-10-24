<?php

namespace tests;

use PeacefulBit\Packet\Calculator;
use PeacefulBit\Packet\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;

class MathModuleTest extends TestCase
{
    /**
     * @var Calculator
     */
    private $calc;

    public function setUp()
    {
        $this->calc = new Calculator();
    }

    public function testSimpleExpression()
    {
        $this->assertEquals(10, $this->calc->calculate('10'));
        $this->assertEquals(5, $this->calc->calculate('10 5'));
    }

    public function testAdd()
    {
        $this->assertEquals(7, $this->calc->calculate('(+ 2 5)'));
        $this->assertEquals(6, $this->calc->calculate('(+ 1 2 3)'));
        $this->assertEquals(0, $this->calc->calculate('(+)'));
    }

    public function testMul()
    {
        $this->assertEquals(8, $this->calc->calculate('(* 2 4)'));
        $this->assertEquals(5, $this->calc->calculate('(* 5)'));
        $this->assertEquals(1, $this->calc->calculate('(*)'));
    }

    public function testSub()
    {
        $this->assertEquals(7, $this->calc->calculate('(- 12 5)'));
        $this->assertEquals(-5, $this->calc->calculate('(- 5)'));
        $this->assertEquals(4, $this->calc->calculate('(- 10 2 4)'));
    }

    public function testDiv()
    {
        $this->assertEquals(6, $this->calc->calculate('(/ 24 4)'));
        $this->assertEquals(1 / 5, $this->calc->calculate('(/ 5)'));
        $this->assertEquals(2, $this->calc->calculate('(/ 24 2 6)'));
    }

    public function testPow()
    {
        $this->assertEquals(9, $this->calc->calculate('(pow 3 2)'));
        $this->assertEquals(27, $this->calc->calculate('(pow 3 3)'));
        $this->assertEquals(729, $this->calc->calculate('(pow 3 3 2)'));
    }

    public function testMod()
    {
        $this->assertEquals(0, $this->calc->calculate('(% 9 3)'));
        $this->assertEquals(1, $this->calc->calculate('(% 9 2)'));
        $this->assertEquals(18, $this->calc->calculate('(% 18 27)'));
    }

    public function testBadSub()
    {
        $this->expectException(RuntimeException::class);
        $this->calc->calculate(('(-)'));
    }

    public function testBadDiv()
    {
        $this->expectException(RuntimeException::class);
        $this->calc->calculate(('(/)'));
    }

    public function testBadMod()
    {
        $this->expectException(RuntimeException::class);
        $this->calc->calculate(('(%)'));
    }

    public function testBadPow()
    {
        $this->expectException(RuntimeException::class);
        $this->calc->calculate(('(pow)'));
    }
}
