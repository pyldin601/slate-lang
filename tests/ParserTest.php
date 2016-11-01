<?php

namespace tests;

use PeacefulBit\Slate\Exceptions\ParserException;
use PeacefulBit\Slate\Parser\Parser;
use PHPUnit\Framework\TestCase;

use PeacefulBit\Slate\Parser\Nodes;
use PeacefulBit\Slate\Parser\Tokens;

class ParserTest extends TestCase
{
    /**
     * @var Parser
     */
    private $parser;

    public function setUp()
    {
        $this->parser = new Parser;
    }

    public function testEmptyTree()
    {
        /**
         * @var Nodes\Program $ast
         */
        $ast = $this->parser->parse([]);

        $this->assertEquals('', strval($ast));
    }

    public function testSymbolNode()
    {
        $ast = $this->parser->parse([
            new Tokens\IdentifierToken('foo')
        ]);

        $this->assertEquals('foo', strval($ast));
    }

    public function testStringNode()
    {
        $ast = $this->parser->parse([
            new Tokens\StringToken('foo')
        ]);

        $this->assertEquals('"foo"', strval($ast));
    }

    public function testDefineConstant()
    {
        $tokens = [
            new Tokens\OpenBracketToken(),
            new Tokens\IdentifierToken('def'),
            new Tokens\IdentifierToken('foo'),
            new Tokens\NumericToken('12'),
            new Tokens\IdentifierToken('baz'),
            new Tokens\StringToken('bas'),
            new Tokens\CloseBracketToken()
        ];

        $ast = $this->parser->parse($tokens);

        $this->assertEquals('(def foo 12 baz "bas")', strval($ast));
    }

    public function testDefineFunction()
    {
        $tokens = [
            new Tokens\OpenBracketToken(),
            new Tokens\IdentifierToken('def'),
            new Tokens\OpenBracketToken(),
            new Tokens\IdentifierToken('func'),
            new Tokens\IdentifierToken('a'),
            new Tokens\IdentifierToken('b'),
            new Tokens\CloseBracketToken(),
            new Tokens\IdentifierToken('a'),
            new Tokens\CloseBracketToken()
        ];

        $ast = $this->parser->parse($tokens);

        $this->assertEquals('(def (func a b) a)', strval($ast));
    }

    public function testFunctionInvocation()
    {
        $tokens = [
            new Tokens\OpenBracketToken(),
            new Tokens\IdentifierToken('foo'),
            new Tokens\IdentifierToken('a'),
            new Tokens\IdentifierToken('b'),
            new Tokens\CloseBracketToken()
        ];

        $ast = $this->parser->parse($tokens);

        $this->assertEquals('(foo a b)', strval($ast));
    }

    public function testIfExpression()
    {
        $tokens = [
            new Tokens\OpenBracketToken(),
            new Tokens\IdentifierToken('if'),
            new Tokens\IdentifierToken('a'),
            new Tokens\IdentifierToken('b'),
            new Tokens\IdentifierToken('c'),
            new Tokens\CloseBracketToken()
        ];

        $ast = $this->parser->parse($tokens);

        $this->assertEquals('(if a b c)', strval($ast));
    }

    public function testOrExpression()
    {
        $tokens = [
            new Tokens\OpenBracketToken(),
            new Tokens\IdentifierToken('or'),
            new Tokens\IdentifierToken('a'),
            new Tokens\IdentifierToken('b'),
            new Tokens\CloseBracketToken()
        ];

        $ast = $this->parser->parse($tokens);

        $this->assertEquals('(or a b)', strval($ast));
    }

    public function testInvalidOrExpression()
    {
        $tokens = [
            new Tokens\OpenBracketToken(),
            new Tokens\IdentifierToken('or'),
            new Tokens\CloseBracketToken()
        ];

        $this->expectException(ParserException::class);
        $this->expectExceptionMessage("'or' requires at least one argument");

        $this->parser->parse($tokens);
    }

    public function testAndExpression()
    {
        $tokens = [
            new Tokens\OpenBracketToken(),
            new Tokens\IdentifierToken('and'),
            new Tokens\IdentifierToken('a'),
            new Tokens\IdentifierToken('b'),
            new Tokens\CloseBracketToken()
        ];

        $ast = $this->parser->parse($tokens);

        $this->assertEquals('(and a b)', strval($ast));
    }

    public function testInvalidAndExpression()
    {
        $tokens = [
            new Tokens\OpenBracketToken(),
            new Tokens\IdentifierToken('and'),
            new Tokens\CloseBracketToken()
        ];

        $this->expectException(ParserException::class);
        $this->expectExceptionMessage("'and' requires at least one argument");

        $this->parser->parse($tokens);
    }
}
