<?php

namespace tests;

use PHPUnit\Framework\TestCase;

class CalculusTest extends TestCase
{
    private $vm;

    public function setUp()
    {
        $this->vm = \PeacefulBit\LispMachine\VM\initDefaultModules();
    }

    public function exec($code)
    {
        return call_user_func($this->vm, $code);
    }

    public function testEmptyExpression()
    {
        $this->assertNull($this->exec(""));
        $this->assertEquals(1, $this->exec("1"));
        $this->assertEquals(5, $this->exec("1 5"));
        $this->assertEquals(10, $this->exec("(+ 1 9)"));
    }
}
