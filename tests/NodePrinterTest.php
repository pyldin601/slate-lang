<?php

namespace tests;

use PeacefulBit\Packet\Nodes\ConstantNode;
use PeacefulBit\Packet\Nodes\FunctionNode;
use PeacefulBit\Packet\Nodes\InvokeNode;
use PeacefulBit\Packet\Nodes\NativeNode;
use PeacefulBit\Packet\Nodes\SequenceNode;
use PeacefulBit\Packet\Nodes\StringNode;
use PeacefulBit\Packet\Nodes\SymbolNode;
use PeacefulBit\Packet\Visitors\NodePrinterVisitor;
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
        $func = function () {
            //
        };

        $this->assertEquals('(native foo)', $this->str(new NativeNode('foo', $func)));
    }

    private function str($node)
    {
        return (new NodePrinterVisitor)->visit($node);
    }
}
