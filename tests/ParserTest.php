<?php

namespace tests;

use PeacefulBit\LispMachine\Parser\ParserException;
use PHPUnit\Framework\TestCase;

use PeacefulBit\LispMachine\Parser;
use PeacefulBit\LispMachine\Lexer;

class ParserTest extends TestCase
{
    public function testEmptyExpression()
    {
        $expression = "";
        $result = Parser\toLexemes($expression);
        $this->assertEmpty($result);
    }

    public function testSymbol()
    {
        $lexemes = Parser\toLexemes("some_symbol");
        $this->assertCount(1, $lexemes);

        $lexeme = $lexemes[0];

        $this->assertEquals(Lexer\LEXEME_SYMBOL, $lexeme[0]);
        $this->assertEquals("some_symbol", implode("", $lexeme[1]));
    }

    public function testUnescapedString()
    {
        $lexemes = Parser\toLexemes('"hello world"');
        $this->assertCount(1, $lexemes);

        $lexeme = $lexemes[0];

        $this->assertEquals(Lexer\LEXEME_STRING, $lexeme[0]);
        $this->assertEquals("hello world", implode("", $lexeme[1]));
    }

    public function testEscapedString()
    {
        $lexemes = Parser\toLexemes('"hello \"world\""');
        $this->assertCount(1, $lexemes);

        $lexeme = $lexemes[0];

        $this->assertEquals(Lexer\LEXEME_STRING, $lexeme[0]);
        $this->assertEquals("hello \"world\"", implode("", $lexeme[1]));
    }

    public function testBrackets()
    {
        $lexemes = Parser\toLexemes('()');
        $this->assertCount(2, $lexemes);

        $this->assertEquals(Lexer\LEXEME_OPEN_BRACKET, $lexemes[0][0]);
        $this->assertEquals(Lexer\LEXEME_CLOSE_BRACKET, $lexemes[1][0]);
    }

    public function testUnclosedString()
    {
        try {
            Parser\toLexemes('"hello');
            $this->fail("Exception must be thrown");
        } catch (ParserException $exception) {
            $this->assertEquals("Unexpected end of string after \"hello\"", $exception->getMessage());
        }
    }

    public function testUnusedEscape()
    {
        try {
            Parser\toLexemes('"hello\\');
            $this->fail("Exception must be thrown");
        } catch (ParserException $exception) {
            $this->assertEquals("Unused escape character after \"hello\"", $exception->getMessage());
        }
    }
}
