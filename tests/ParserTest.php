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

        $this->assertEquals(Lexer\LEXEME_SYMBOL, Lexer\getType($lexeme));
        $this->assertEquals("some_symbol", implode("", Lexer\getData($lexeme)));
    }

    public function testDelimiters()
    {
        $this->assertCount(2, Parser\toLexemes("foo bar"));
        $this->assertCount(2, Parser\toLexemes("foo \t bar"));
        $this->assertCount(2, Parser\toLexemes("foo \r\n bar"));
    }

    public function testUnescapedString()
    {
        $lexemes = Parser\toLexemes('"hello world"');
        $this->assertCount(1, $lexemes);

        $lexeme = $lexemes[0];

        $this->assertEquals(Lexer\LEXEME_STRING, Lexer\getType($lexeme));
        $this->assertEquals("hello world", implode("", $lexeme[1]));
    }

    public function testEscapedString()
    {
        $lexemes = Parser\toLexemes('"hello \"world\""');
        $this->assertCount(1, $lexemes);

        $lexeme = $lexemes[0];

        $this->assertEquals(Lexer\LEXEME_STRING, Lexer\getType($lexeme));
        $this->assertEquals("hello \"world\"", implode("", Lexer\getData($lexeme)));
    }

    public function testBrackets()
    {
        $lexemes = Parser\toLexemes('()');
        $this->assertCount(2, $lexemes);

        $this->assertEquals(Lexer\LEXEME_OPEN_BRACKET, Lexer\getType($lexemes[0]));
        $this->assertEquals(Lexer\LEXEME_CLOSE_BRACKET, Lexer\getType($lexemes[1]));
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

    public function testParseSimpleProgram()
    {
        $code = '(+ 5.6 2.7 (- 15 (/ 4 2)))';
        $lexemes = Parser\toLexemes($code);

        $this->assertCount(14, $lexemes);

        $expectedLexemes = [
            Lexer\LEXEME_OPEN_BRACKET,  // (
            Lexer\LEXEME_SYMBOL,        // +
            Lexer\LEXEME_SYMBOL,        // 5.6
            Lexer\LEXEME_SYMBOL,        // 2.7
            Lexer\LEXEME_OPEN_BRACKET,  // (
            Lexer\LEXEME_SYMBOL,        // -
            Lexer\LEXEME_SYMBOL,        // 15
            Lexer\LEXEME_OPEN_BRACKET,  // (
            Lexer\LEXEME_SYMBOL,        // /
            Lexer\LEXEME_SYMBOL,        // 4
            Lexer\LEXEME_SYMBOL,        // 2
            Lexer\LEXEME_CLOSE_BRACKET, // )
            Lexer\LEXEME_CLOSE_BRACKET, // )
            Lexer\LEXEME_CLOSE_BRACKET  // )
        ];

        $types = array_map('\PeacefulBit\LispMachine\Lexer\getType', $lexemes);

        $this->assertEquals($expectedLexemes, $types);
    }

    public function testComments()
    {
        $code = '(+ 1 2) ; This must be ignored';
        $lexemes = Parser\toLexemes($code);

        $this->assertCount(5, $lexemes);
    }
}
