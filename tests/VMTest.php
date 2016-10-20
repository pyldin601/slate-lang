<?php

namespace tests;

use PHPUnit\Framework\TestCase;

class VMTest extends TestCase
{
    public function testSimpleExpression()
    {
        $this->assertEquals(10, $this->evaluate('10'));
        $this->assertEquals(5, $this->evaluate('10 5'));

        $this->assertEquals(7, $this->evaluate('(+ 2 5)'));
        $this->assertEquals(6, $this->evaluate('(+ 1 2 3)'));
        $this->assertEquals(0, $this->evaluate('(+)'));

        $this->assertEquals(7, $this->evaluate('(- 12 5)'));
        $this->assertEquals(-5, $this->evaluate('(- 5)'));
        $this->assertEquals(4, $this->evaluate('(- 10 2 4)'));

        $this->assertEquals(8, $this->evaluate('(* 2 4)'));
        $this->assertEquals(5, $this->evaluate('(* 5)'));
        $this->assertEquals(1, $this->evaluate('(*)'));

        $this->assertEquals(6, $this->evaluate('(/ 24 4)'));
        $this->assertEquals(1 / 5, $this->evaluate('(/ 5)'));
        $this->assertEquals(2, $this->evaluate('(/ 24 2 6)'));
    }

    private function evaluate($code)
    {
        $lexemes = \PeacefulBit\LispMachine\Parser\toLexemes($code);
        $ast = \PeacefulBit\LispMachine\Parser\toAst($lexemes);
        return \PeacefulBit\LispMachine\VM\evaluate($ast);
    }
}
