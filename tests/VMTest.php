<?php

namespace tests;

use PHPUnit\Framework\TestCase;

abstract class VMTest extends TestCase
{


    public function testNestedExpression()
    {
        $this->assertEquals(23, $this->exec('
            (+ 5 
               10 
               (+ 1 
                  2) 
               (/ 10 
                  2))
        '));
    }

    public function testLogicalExpression()
    {
        $this->assertTrue($this->exec('(or 5 10)'));
        $this->assertTrue($this->exec('(or 0 10)'));

        $this->assertTrue($this->exec('(and 5 15)'));
        $this->assertFalse($this->exec('(and 0 15)'));

        $this->assertTrue($this->exec('(not 0)'));
        $this->assertFalse($this->exec('(not 15)'));
    }

    public function testRelations()
    {
        $this->assertTrue($this->exec('(> 5 2)'));
        $this->assertFalse($this->exec('(< 5 2)'));
        $this->assertTrue($this->exec('(= 5 (+ 2 3))'));
        $this->assertFalse($this->exec('(> 5 2 3)'));
        $this->assertTrue($this->exec('(> 15 12 3)'));
        $this->assertTrue($this->exec('(!= 5 6)'));
        $this->assertTrue($this->exec('(!== "hello" 6)'));
    }

    public function testIfAndUnless()
    {
        $this->assertEquals(3, $this->exec('(if (> 5 2) 3 5)'));
        $this->assertEquals(5, $this->exec('(unless (> 5 2) 3 5)'));
    }

    public function testVariablesDeclaration()
    {
        $code = '(def pi 3.14) (* pi 2)';
        $this->assertEquals(6.28, $this->exec($code));
    }

    public function testFunctionDeclaration()
    {
        $code = '(def (square x) (* x x)) (square 3)';
        $this->assertEquals(9, $this->exec($code));
    }


    public function testFunctionOverrideTest()
    {
        $this->assertEquals(10, $this->exec('(+ 2 8)'));
        $this->assertEquals(16, $this->exec('(def (+ x y) (* x y)) (+ 2 8)'));
    }

    private function exec($code)
    {
        return call_user_func(\PeacefulBit\LispMachine\VM\initDefaultModules(), $code);
    }
}
