<?php

namespace PeacefulBit\Packet\Visitors;

use PeacefulBit\Packet\Context\Context;
use PeacefulBit\Packet\Context\JobQueue;
use PeacefulBit\Packet\Context\Stack;
use PeacefulBit\Packet\Exception\RuntimeException;
use PeacefulBit\Packet\Nodes;

class StackBasedNodeVisitor implements NodeVisitor
{
    use NodeDispatchingTrait;

    /**
     * @var Stack
     */
    private $stack;

    /**
     * @var JobQueue
     */
    private $queue;

    /**
     * @var Context
     */
    private $context;

    /**
     * @param Stack $stack
     * @param JobQueue $queue
     * @param Context $context
     */
    public function __construct($stack, $queue, $context)
    {
        $this->stack = $stack;
        $this->queue = $queue;
        $this->context = $context;
    }

    public function visitConstantNode(Nodes\ConstantNode $node)
    {
        //
    }

    public function visitFunctionNode(Nodes\FunctionNode $node)
    {
        //
    }

    public function visitInvokeNode(Nodes\InvokeNode $node)
    {
        //
    }

    public function visitSequenceNode(Nodes\SequenceNode $seq)
    {
        //
    }

    public function visitStringNode(Nodes\StringNode $node)
    {
        //
    }

    public function visitSymbolNode(Nodes\SymbolNode $node)
    {
        //
    }

    public function visitNativeNode(Nodes\NativeNode $node)
    {
        //
    }

    public function visitLambdaNode(Nodes\LambdaNode $node)
    {
        //
    }
}
