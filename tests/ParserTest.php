<?php

namespace tests;

use PeacefulBit\LispMachine\Parser\ParserException;
use PeacefulBit\Pocket\Exception\TokenizerException;
use PeacefulBit\Pocket\Parser\Tokenizer;
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
        $tokens = Tokenizer::tokenize("some_symbol");
        $this->assertCount(1, $tokens);

        $token = $tokens[0];

        $this->assertEquals("SymbolToken(some_symbol)", (string) $token);
    }

    public function testDelimiters()
    {
        $this->assertCount(2, Tokenizer::tokenize("foo bar"));
        $this->assertCount(2, Tokenizer::tokenize("foo \t bar"));
        $this->assertCount(2, Tokenizer::tokenize("foo \r\n bar"));
    }

    public function testUnescapedString()
    {
        $tokens = Tokenizer::tokenize('"hello world"');
        $this->assertCount(1, $tokens);

        $token = $tokens[0];

        $this->assertEquals("StringToken(hello world)", $token);
    }

    public function testEscapedString()
    {
        $tokens = Tokenizer::tokenize('"hello \"world\""');
        $this->assertCount(1, $tokens);

        $token = $tokens[0];

        $this->assertEquals("StringToken(hello \"world\")", $token);
    }

    public function testBrackets()
    {
        $tokens = Tokenizer::tokenize('()');
        $this->assertCount(2, $tokens);

        $this->assertEquals('OpenBracketToken CloseBracketToken', implode(' ', $tokens));
    }

    public function testUnclosedString()
    {
        try {
            Tokenizer::tokenize('"hello');
            $this->fail("Exception must be thrown");
        } catch (TokenizerException $exception) {
            $this->assertEquals("Unexpected end of string", $exception->getMessage());
        }
    }

    public function testUnusedEscape()
    {
        try {
            Tokenizer::tokenize('"hello\\');
            $this->fail("Exception must be thrown");
        } catch (TokenizerException $exception) {
            $this->assertEquals("Unused escape character", $exception->getMessage());
        }
    }

    public function testParseSimpleProgram()
    {
        $code = '(+ 5.6 2.7 (- 15 (/ 4 2)))';
        $tokens = Tokenizer::tokenize($code);

        $this->assertCount(14, $tokens);

        $expectedTokens = [
            'OpenBracketToken',
            'SymbolToken(+)',
            'SymbolToken(5.6)',
            'SymbolToken(2.7)',
            'OpenBracketToken',
            'SymbolToken(-)',
            'SymbolToken(15)',
            'OpenBracketToken',
            'SymbolToken(/)',
            'SymbolToken(4)',
            'SymbolToken(2)',
            'CloseBracketToken',
            'CloseBracketToken',
            'CloseBracketToken'
        ];

        $expectedString = implode(' ', $expectedTokens);

        $this->assertEquals($expectedString, implode(' ', $tokens));
    }

    public function testComments()
    {
        $code = '(+ 1 2) ; This must be ignored';
        $lexemes = Tokenizer::tokenize($code);

        $this->assertCount(6, $lexemes);
    }
}
