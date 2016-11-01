<?php

namespace tests;

use PeacefulBit\Slate\Exceptions\TokenizerException;
use PeacefulBit\Slate\Parser\Tokenizer;
use PeacefulBit\Slate\Parser\Tokens\CloseBracketToken;
use PeacefulBit\Slate\Parser\Tokens\IdentifierToken;
use PeacefulBit\Slate\Parser\Tokens\NumericToken;
use PeacefulBit\Slate\Parser\Tokens\OpenBracketToken;
use PeacefulBit\Slate\Parser\Tokens\StringToken;
use PHPUnit\Framework\TestCase;

class TokenizerTest extends TestCase
{
    /**
     * @var Tokenizer
     */
    private $tokenizer;

    public function setUp()
    {
        $this->tokenizer = new Tokenizer();
    }

    public function testEmptyExpression()
    {
        $result = $this->tokenizer->tokenize("");
        $this->assertEmpty($result);
    }

    public function testSymbol()
    {
        $tokens = $this->tokenizer->tokenize("some");
        $this->assertCount(1, $tokens);
        $this->assertInstanceOf(IdentifierToken::class, $tokens[0]);
        $this->assertEquals('id:some', $tokens[0]);
    }

    public function testString()
    {
        $tokens = $this->tokenizer->tokenize("\"some\"");
        $this->assertCount(1, $tokens);
        $this->assertInstanceOf(StringToken::class, $tokens[0]);
        $this->assertEquals('string:some', $tokens[0]);
    }

    public function testEscapedString()
    {
        $tokens = $this->tokenizer->tokenize('"\"world\""');
        $this->assertCount(1, $tokens);

        $this->assertEquals("string:\"world\"", $tokens[0]);
    }

    public function testNumeric()
    {
        $tokens = $this->tokenizer->tokenize("123");
        $this->assertCount(1, $tokens);
        $this->assertInstanceOf(NumericToken::class, $tokens[0]);
        $this->assertEquals('numeric:123', $tokens[0]);
    }

    public function testOpenBracket()
    {
        $tokens = $this->tokenizer->tokenize("(");
        $this->assertCount(1, $tokens);
        $this->assertInstanceOf(OpenBracketToken::class, $tokens[0]);
        $this->assertEquals('bracket:(', $tokens[0]);
    }

    public function testCloseBracket()
    {
        $tokens = $this->tokenizer->tokenize(")");
        $this->assertCount(1, $tokens);
        $this->assertInstanceOf(CloseBracketToken::class, $tokens[0]);
        $this->assertEquals('bracket:)', $tokens[0]);
    }

    public function testMultipleTokens()
    {
        $tokens = $this->tokenizer->tokenize("foo bar");
        $this->assertCount(2, $tokens);

        $this->assertInstanceOf(IdentifierToken::class, $tokens[0]);
        $this->assertInstanceOf(IdentifierToken::class, $tokens[1]);

        $this->assertEquals('id:foo', $tokens[0]);
        $this->assertEquals('id:bar', $tokens[1]);
    }

    public function testIgnoringDelimiters()
    {
        $this->assertCount(2, $this->tokenizer->tokenize("foo      bar"));
        $this->assertCount(2, $this->tokenizer->tokenize("foo \t   bar"));
        $this->assertCount(2, $this->tokenizer->tokenize("foo \r\n bar"));
    }

    public function testUnclosedString()
    {
        $this->expectException(TokenizerException::class);
        $this->expectExceptionMessage("Unexpected end of string");
        $this->tokenizer->tokenize('"hello');
    }

    public function testUnusedEscape()
    {
        $this->expectException(TokenizerException::class);
        $this->expectExceptionMessage("Unused escape character");
        $this->tokenizer->tokenize('"hello\\');
    }

    public function testParseSimpleProgram()
    {
        $code = '(+ 5.6 2.7 (- 15 (/ 4 2)))';
        $tokens = $this->tokenizer->tokenize($code);

        $this->assertCount(14, $tokens);

        $expectedTokens = [
            'bracket:(',
            'id:+',
            'numeric:5.6',
            'numeric:2.7',
            'bracket:(',
            'id:-',
            'numeric:15',
            'bracket:(',
            'id:/',
            'numeric:4',
            'numeric:2',
            'bracket:)',
            'bracket:)',
            'bracket:)'
        ];

        $expectedString = implode(' ', $expectedTokens);

        $this->assertEquals($expectedString, implode(' ', $tokens));
    }

    public function testComments()
    {
        $code = '; This must be ignored';
        $tokens = $this->tokenizer->tokenize($code);

        $this->assertEmpty($tokens);
    }

    public function testSourceCode()
    {
        $file = __DIR__ . '/fixtures/program.pt';
        $code = file_get_contents($file);
        $tokens = $this->tokenizer->tokenize($code);
        $this->assertTrue(is_array($tokens));
    }
}
