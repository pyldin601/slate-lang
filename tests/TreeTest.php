<?php

namespace tests;

use PHPUnit\Framework\TestCase;

use PeacefulBit\LispMachine\Parser;
use PeacefulBit\LispMachine\Lexer;

class TreeTest extends TestCase
{
    public function testEmptyTree()
    {
        $ast = Parser\toAst([]);
        $this->assertCount(0, $ast);
    }

    public function testSimpleExpression()
    {
        $lexemes = Parser\toLexemes("(+ 1 2)");
        $ast = Parser\toAst($lexemes);

        $expected = [
            [
                Lexer\makeLexeme(Lexer\LEXEME_SYMBOL, ['+']),
                Lexer\makeLexeme(Lexer\LEXEME_SYMBOL, ['1']),
                Lexer\makeLexeme(Lexer\LEXEME_SYMBOL, ['2']),
            ]
        ];

        $this->assertEquals($expected, $ast);
    }

    public function testNestedExpression()
    {
        $lexemes = Parser\toLexemes("(+ 1 (- 10 2))");
        $ast = Parser\toAst($lexemes);

        $expected = [
            [
                Lexer\makeLexeme(Lexer\LEXEME_SYMBOL, ['+']),
                Lexer\makeLexeme(Lexer\LEXEME_SYMBOL, ['1']),
                [
                    Lexer\makeLexeme(Lexer\LEXEME_SYMBOL, ['-']),
                    Lexer\makeLexeme(Lexer\LEXEME_SYMBOL, ['1', '0']),
                    Lexer\makeLexeme(Lexer\LEXEME_SYMBOL, ['2'])
                ]
            ]
        ];

        $this->assertEquals($expected, $ast);
    }
}
