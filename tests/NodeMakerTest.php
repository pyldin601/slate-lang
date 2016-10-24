<?php

namespace tests;

use function Nerd\Common\Arrays\all;
use PeacefulBit\Packet\Parser\Tokenizer;
use PHPUnit\Framework\TestCase;

use PeacefulBit\Packet\Nodes;
use PeacefulBit\Packet\Tokens;

class NodeMakerTest extends TestCase
{
    /**
     * @var Tokenizer
     */
    private $tokenizer;

    public function setUp()
    {
        $this->tokenizer = new Tokenizer;
    }

    public function testEmptyTree()
    {
        $tree = $this->tokenizer->convertSequenceToNode([]);
        $this->assertTrue($tree instanceof Nodes\SequenceNode);
        $this->assertCount(0, $tree->getNodes());
    }

    public function testSymbolNode()
    {
        $token = new Tokens\SymbolToken('foo');
        $node = $this->tokenizer->convertToNode($token);

        $this->assertTrue($node instanceof Nodes\SymbolNode);
        $this->assertEquals('foo', $node->getName());
    }

    public function testStringNode()
    {
        $token = new Tokens\StringToken('foo');
        $node = $this->tokenizer->convertToNode($token);

        $this->assertTrue($node instanceof Nodes\StringNode);
        $this->assertEquals('foo', $node->getValue());
    }

    public function testDefineConstant()
    {
        $tokens = [
            new Tokens\SymbolToken('def'),
            new Tokens\SymbolToken('foo'),
            new Tokens\SymbolToken('bar'),
            new Tokens\SymbolToken('baz'),
            new Tokens\SymbolToken('bas')
        ];

        $node = $this->tokenizer->convertToNode($tokens);

        $this->assertInstanceOf(Nodes\ConstantNode::class, $node);
        $this->assertEquals(['foo', 'baz'], $node->getNames());
        $this->assertEquals(['bar', 'bas'], array_map(function ($argument) {
            $this->assertInstanceOf(Nodes\SymbolNode::class, $argument);
            return $argument->getName();
        }, $node->getValues()));
    }

    public function testDefineFunction()
    {
        $tokens = [
            new Tokens\SymbolToken('def'),
            [
                new Tokens\SymbolToken('func'),
                new Tokens\SymbolToken('a'),
                new Tokens\SymbolToken('b')
            ],
            new Tokens\SymbolToken('a')
        ];

        $node = $this->tokenizer->convertToNode($tokens);

        $this->assertInstanceOf(Nodes\FunctionNode::class, $node);
        $this->assertEquals('func', $node->getName());
        $this->assertEquals(['a', 'b'], $node->getArguments());
        $this->assertInstanceOf(Nodes\SequenceNode::class, $node->getBody());

        $body = $node->getBody()->getNodes();

        $this->assertCount(1, $body);

        $this->assertInstanceOf(Nodes\SymbolNode::class, $body[0]);
        $this->assertEquals('a', $body[0]->getName());
    }

    public function testFunctionInvocation()
    {
        $tokens = [
            new Tokens\SymbolToken('foo'),
            new Tokens\SymbolToken('a'),
            new Tokens\SymbolToken('b')
        ];

        $node = $this->tokenizer->convertToNode($tokens);

        $this->assertInstanceOf(Nodes\InvokeNode::class, $node);

        $this->assertInstanceOf(Nodes\SymbolNode::class, $node->getFunction());
        $this->assertEquals('foo', $node->getFunction()->getName());
        $this->assertCount(2, $node->getArguments());
        $this->assertTrue(all($node->getArguments(), function ($arg) {
            return $arg instanceof Nodes\SymbolNode;
        }), 'All arguments must be a symbols');
        $this->assertEquals(['a', 'b'], array_map(function ($node) {
            return $node->getName();
        }, $node->getArguments()));
    }
}
