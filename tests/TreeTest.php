<?php

namespace tests;

use PHPUnit\Framework\TestCase;

use PeacefulBit\LispMachine\Parser;
use PeacefulBit\LispMachine\Tree;

/**
 * Class TreeTest
 * @package tests
 * @after ParserTest
 */
class TreeTest extends TestCase
{
    public function testEmptyTree()
    {
        $ast = Parser\toAst([]);
        $this->assertTrue(Tree\isNode($ast));
        $this->assertEquals(Tree\TYPE_SEQUENCE, Tree\typeOf($ast));
        $this->assertCount(0, Tree\valueOf($ast));
    }

    public function testMultiSymbol()
    {
        $lexemes = Parser\toLexemes("1 2");
        $ast = Parser\toAst($lexemes);

        $this->assertTrue(Tree\isNode($ast));
        $this->assertEquals(Tree\TYPE_SEQUENCE, Tree\typeOf($ast));
        $this->assertCount(2, Tree\valueOf($ast));

        $expected = Tree\node(Tree\TYPE_SEQUENCE, [
            Tree\node(Tree\TYPE_SYMBOL, '1'),
            Tree\node(Tree\TYPE_SYMBOL, '2'),
        ]);

        $this->assertEquals($expected, $ast);
    }

    public function testSimpleExpression()
    {
        $lexemes = Parser\toLexemes("(+ 1 2)");
        $ast = Parser\toAst($lexemes);

        $expected = Tree\node(Tree\TYPE_SEQUENCE, [
            Tree\node(Tree\TYPE_EXPRESSION, [
                Tree\node(Tree\TYPE_SYMBOL, '+'),
                Tree\node(Tree\TYPE_SYMBOL, '1'),
                Tree\node(Tree\TYPE_SYMBOL, '2'),
            ])
        ]);

        $this->assertEquals($expected, $ast);
    }

    public function testNestedExpression()
    {
        $lexemes = Parser\toLexemes("(+ 1 (- 10 2))");
        $ast = Parser\toAst($lexemes);

        $expected = Tree\node(Tree\TYPE_SEQUENCE, [
            Tree\node(Tree\TYPE_EXPRESSION, [
                Tree\node(Tree\TYPE_SYMBOL, '+'),
                Tree\node(Tree\TYPE_SYMBOL, '1'),
                Tree\node(Tree\TYPE_EXPRESSION, [
                    Tree\node(Tree\TYPE_SYMBOL, '-'),
                    Tree\node(Tree\TYPE_SYMBOL, '10'),
                    Tree\node(Tree\TYPE_SYMBOL, '2')
                ])
            ])
        ]);

        $this->assertEquals($expected, $ast);
    }

    public function testSequenceOfExpressions()
    {
        $lexemes = Parser\toLexemes("(+ 1 2) (- 10 2)");
        $ast = Parser\toAst($lexemes);

        $expected = Tree\node(Tree\TYPE_SEQUENCE, [
            Tree\node(Tree\TYPE_EXPRESSION, [
                Tree\node(Tree\TYPE_SYMBOL, '+'),
                Tree\node(Tree\TYPE_SYMBOL, '1'),
                Tree\node(Tree\TYPE_SYMBOL, '2')
            ]),
            Tree\node(Tree\TYPE_EXPRESSION, [
                Tree\node(Tree\TYPE_SYMBOL, '-'),
                Tree\node(Tree\TYPE_SYMBOL, '10'),
                Tree\node(Tree\TYPE_SYMBOL, '2'),
            ])
        ]);

        $this->assertEquals($expected, $ast);
    }
}
