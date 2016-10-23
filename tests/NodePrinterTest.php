<?php

namespace tests;

use PeacefulBit\Pocket\Parser\Nodes\ConstantNode;
use PeacefulBit\Pocket\Parser\Nodes\FunctionNode;
use PeacefulBit\Pocket\Parser\Nodes\InvokeNode;
use PeacefulBit\Pocket\Parser\Nodes\NativeNode;
use PeacefulBit\Pocket\Parser\Nodes\SequenceNode;
use PeacefulBit\Pocket\Parser\Nodes\StringNode;
use PeacefulBit\Pocket\Parser\Nodes\SymbolNode;
use PeacefulBit\Pocket\Parser\Visitors\NodePrinterVisitor;
use PHPUnit\Framework\TestCase;

class NodePrinterTest extends TestCase
{
    public function testSymbolNode()
    {
        $node = new SymbolNode('foo');
        $this->assertEquals('foo', $this->str($node));
    }

    public function testStringNode()
    {
        $this->assertEquals('"hello, world!"', $this->str(new StringNode('hello, world!')));
        $this->assertEquals('"hello\\"world!"', $this->str(new StringNode('hello"world!')));
    }

    public function testSequenceNode()
    {
        $foo = new SymbolNode('foo');
        $bar = new SymbolNode('bar');
        $baz = new StringNode('baz');

        $node = new SequenceNode([$foo, $bar, $baz]);

        $this->assertEquals('foo bar "baz"', $this->str($node));
    }

    public function testInvokeNode()
    {
        $function = new SymbolNode('f');
        $arguments = [new SymbolNode('10'), new SymbolNode('5')];

        $node = new InvokeNode($function, $arguments);

        $this->assertEquals('(f 10 5)', $this->str($node));
    }

    public function testFunctionDeclareNode()
    {
        $node = new FunctionNode('func', ['a', 'b', 'c'], new SymbolNode('a'));

        $this->assertEquals('(def (func a b c) a)', $this->str($node));
    }

    public function testConstDeclareNode()
    {
        $node = new ConstantNode(['a', 'b'], [new SymbolNode('10'), new SymbolNode('15')]);

        $this->assertEquals('(def a 10 b 15)', $this->str($node));
    }

    public function testNativeNode()
    {
        $this->assertEquals('(native foo)', $this->str(new NativeNode('foo')));
    }

    private function str($node)
    {
        return (new NodePrinterVisitor)->visit($node);
    }
}
