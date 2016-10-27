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
        $this->assertEquals('foo', $this->exec(new Nodes\StringNode('foo')));
        $this->assertEquals('test message', $this->exec(new Nodes\SymbolNode('test')));
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

        $visitor->valueOf($node);

        $queue->run();

        $this->assertEquals(1, $stack->size(), 'Stack must contain returned value.');

        return $stack->shift();
    }
}
