<?php

namespace tests;

use PeacefulBit\Packet\Calculator;
use PHPUnit\Framework\TestCase;

class LogicModuleTest extends TestCase
{
    /**
     * @var Calculator
     */
    private $calc;

    public function setUp()
    {
        $this->calc = new Calculator();
    }

    public function testOr()
    {
        $this->assertTrue($this->calc->calculate('(or 1 2)'));
        $this->assertTrue($this->calc->calculate('(or 1 0)'));
        $this->assertFalse($this->calc->calculate('(or 0 0)'));
    }

    public function testAnd()
    {
        $this->assertTrue($this->calc->calculate('(and 1 2)'));
        $this->assertFalse($this->calc->calculate('(and 1 0)'));
        $this->assertFalse($this->calc->calculate('(and 0 0)'));
    }

    public function testNot()
    {
        $this->assertFalse($this->calc->calculate('(not 1 2)'));
        $this->assertFalse($this->calc->calculate('(not 1 0)'));
        $this->assertTrue($this->calc->calculate('(not 0)'));
    }

    public function testIf()
    {
        $this->assertEquals(2, $this->calc->calculate('(if 1 2 3)'));
        $this->assertEquals(3, $this->calc->calculate('(if 0 2 3)'));
        $this->assertEquals(3, $this->calc->calculate('(if (if 0 1 0) 2 3)'));
    }

    public function testUnless()
    {
        $this->assertEquals(3, $this->calc->calculate('(unless 1 2 3)'));
        $this->assertEquals(2, $this->calc->calculate('(unless 0 2 3)'));
        $this->assertEquals(3, $this->calc->calculate('(unless (unless 0 1 0) 2 3)'));
    }
}
