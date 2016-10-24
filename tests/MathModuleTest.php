<?php

namespace tests;

use PeacefulBit\Packet\Calculator;
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
}
