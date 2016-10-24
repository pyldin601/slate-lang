<?php

namespace tests;

use PeacefulBit\Packet\Calculator;
use PeacefulBit\Packet\Exception\RuntimeException;
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

        $this->expectException(RuntimeException::class);
        $this->calc->calculate('(>)');
    }

    public function testLs()
    {
        $this->assertFalse($this->calc->calculate('(< 5 2)'));
        $this->assertTrue($this->calc->calculate('(< 2 5)'));
        $this->assertFalse($this->calc->calculate('(< 5 5)'));

        $this->expectException(RuntimeException::class);
        $this->calc->calculate('(<)');
    }

    public function testGeq()
    {
        $this->assertTrue($this->calc->calculate('(>= 5 2)'));
        $this->assertFalse($this->calc->calculate('(>= 2 5)'));
        $this->assertTrue($this->calc->calculate('(>= 5 5)'));

        $this->expectException(RuntimeException::class);
        $this->calc->calculate('(>=)');
    }

    public function testLeq()
    {
        $this->assertFalse($this->calc->calculate('(<= 5 2)'));
        $this->assertTrue($this->calc->calculate('(<= 2 5)'));
        $this->assertTrue($this->calc->calculate('(<= 5 5)'));

        $this->expectException(RuntimeException::class);
        $this->calc->calculate('(<=)');
    }

    public function testEq()
    {
        $this->assertFalse($this->calc->calculate('(= 5 2)'));
        $this->assertFalse($this->calc->calculate('(= 2 5)'));
        $this->assertTrue($this->calc->calculate('(= 5 5)'));
        $this->assertTrue($this->calc->calculate('(= "0" 0)'));

        $this->expectException(RuntimeException::class);
        $this->calc->calculate('(=)');
    }

    public function testStrictEq()
    {
        $this->assertFalse($this->calc->calculate('(== 5 2)'));
        $this->assertFalse($this->calc->calculate('(== 2 5)'));
        $this->assertTrue($this->calc->calculate('(== 5 5)'));
        $this->assertFalse($this->calc->calculate('(== "0" 0)'));

        $this->expectException(RuntimeException::class);
        $this->calc->calculate('(==)');
    }

    public function testNeq()
    {
        $this->assertTrue($this->calc->calculate('(!= 5 2)'));
        $this->assertTrue($this->calc->calculate('(!= 2 5)'));
        $this->assertFalse($this->calc->calculate('(!= 5 5)'));
        $this->assertFalse($this->calc->calculate('(!= "0" 0)'));

        $this->expectException(RuntimeException::class);
        $this->calc->calculate('(!=)');
    }

    public function testStrictNeq()
    {
        $this->assertTrue($this->calc->calculate('(!== 5 2)'));
        $this->assertTrue($this->calc->calculate('(!== 2 5)'));
        $this->assertFalse($this->calc->calculate('(!== 5 5)'));
        $this->assertTrue($this->calc->calculate('(!== "0" 0)'));

        $this->expectException(RuntimeException::class);
        $this->calc->calculate('(!==)');
    }
}
