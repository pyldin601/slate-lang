<?php

namespace tests;

use PeacefulBit\Packet\Context\Context;
use PeacefulBit\Packet\Context\JobQueue;
use PeacefulBit\Packet\Context\Stack;
use PeacefulBit\Packet\Visitors\StackBasedNodeVisitor;
use PHPUnit\Framework\TestCase;

use PeacefulBit\Packet\Nodes;

class StackBasedVisitorTest extends TestCase
{
    public function testStringValueOf()
    {
//        $this->assertEquals('foo', $this->exec(new Nodes\StringNode('foo')));
//        $this->assertInstanceOf(Nodes\StringNode::class, $this->exec(new Nodes\SymbolNode('test')));
//        $this->assertEquals('second', $this->exec(new Nodes\SequenceNode([
//            new Nodes\StringNode('first'),
//            new Nodes\StringNode('second')
//        ])));
    }

    private function exec(Nodes\Node $node)
    {
        $queue      = new JobQueue;
        $stack      = new Stack;
        $context    = new Context([
            "test" => new Nodes\StringNode("test message")
        ]);

        $this->assertEquals(0, $stack->size());
        $this->assertEquals(0, $queue->size());

        $visitor    = new StackBasedNodeVisitor($stack, $queue, $context);

        $visitor->visit($node);

        $queue->run();

        $this->assertGreaterThan(0, $stack->size(), 'Stack must contain returned value.');

        return $stack->shift();
    }
}
