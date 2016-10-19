<?php

namespace tests;

use PHPUnit\Framework\TestCase;

use function Lisp\VM\Parser\toLexemes;

class ParserTest extends TestCase
{
    public function testEmptyExpression()
    {
        $expression = "";
        $result = toLexemes($expression);
        $this->assertEmpty($result);
    }

    public function testOnSymbol()
    {
        $lexemes = toLexemes("some_symbol");
        $this->assertCount(1, $lexemes);

        $lexeme = $lexemes[0];

        $this->assertEquals(\Lisp\VM\Parser\LEXEME_SYMBOL, $lexeme[0]);
        $this->assertEquals("some_symbol", implode("", $lexeme[1]));
    }
}
