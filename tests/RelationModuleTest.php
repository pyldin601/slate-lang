<?php

namespace tests;

use PeacefulBit\Packet\Calculator;
use PHPUnit\Framework\TestCase;

class RelationModuleTest extends TestCase
{
    /**
     * @var Calculator
     */
    private $calc;

    public function setUp()
    {
        $this->calc = new Calculator();
    }

    public function testGr()
    {
        $this->assertTrue($this->calc->calculate('(> 5 2)'));
        $this->assertFalse($this->calc->calculate('(> 2 5)'));
        $this->assertFalse($this->calc->calculate('(> 5 5)'));
    }

    public function testLs()
    {
        $this->assertFalse($this->calc->calculate('(< 5 2)'));
        $this->assertTrue($this->calc->calculate('(< 2 5)'));
        $this->assertFalse($this->calc->calculate('(< 5 5)'));
    }

    public function testGeq()
    {
        $this->assertTrue($this->calc->calculate('(>= 5 2)'));
        $this->assertFalse($this->calc->calculate('(>= 2 5)'));
        $this->assertTrue($this->calc->calculate('(>= 5 5)'));
    }

    public function testLeq()
    {
        $this->assertFalse($this->calc->calculate('(<= 5 2)'));
        $this->assertTrue($this->calc->calculate('(<= 2 5)'));
        $this->assertTrue($this->calc->calculate('(<= 5 5)'));
    }

    public function testEq()
    {
        $this->assertFalse($this->calc->calculate('(= 5 2)'));
        $this->assertFalse($this->calc->calculate('(= 2 5)'));
        $this->assertTrue($this->calc->calculate('(= 5 5)'));
        $this->assertTrue($this->calc->calculate('(= "0" 0)'));
    }

    public function testStrictEq()
    {
        $this->assertFalse($this->calc->calculate('(== 5 2)'));
        $this->assertFalse($this->calc->calculate('(== 2 5)'));
        $this->assertTrue($this->calc->calculate('(== 5 5)'));
        $this->assertFalse($this->calc->calculate('(== "0" 0)'));
    }

    public function testNeq()
    {
        $this->assertTrue($this->calc->calculate('(!= 5 2)'));
        $this->assertTrue($this->calc->calculate('(!= 2 5)'));
        $this->assertFalse($this->calc->calculate('(!= 5 5)'));
        $this->assertFalse($this->calc->calculate('(!= "0" 0)'));
    }

    public function testStrictNeq()
    {
        $this->assertTrue($this->calc->calculate('(!== 5 2)'));
        $this->assertTrue($this->calc->calculate('(!== 2 5)'));
        $this->assertFalse($this->calc->calculate('(!== 5 5)'));
        $this->assertTrue($this->calc->calculate('(!== "0" 0)'));
    }
}
