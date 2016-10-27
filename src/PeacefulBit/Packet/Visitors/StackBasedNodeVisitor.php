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
//        $combined = $node->getCombined();
//        $keys = array_keys($combined);
//
//        array_walk($keys, function ($key) use ($combined) {
//            $this->queue->push(function () use ($combined, $key) {
//                $this->stack->push($combined[$key]);
//            });
//            $this->queue->push(function () {
//                $this->stack->apply([$this, 'visit'], 1);
//            });
//            $this->queue->push(function () use ($key) {
//                $this->context->set($key, $this->stack->shift());
//            });
//        });
    }

    public function visitFunctionNode(Nodes\FunctionNode $node)
    {
//        $this->queue->push(function () use ($node) {
//            $this->context->set($node->getName(), $node);
//        });
    }

    public function visitInvokeNode(Nodes\InvokeNode $node)
    {
        //
    }

    public function visitSequenceNode(Nodes\SequenceNode $node)
    {
//        $nodes = $node->getNodes();
//        array_walk($nodes, function (Nodes\Node $node) {
//            $this->queue->push(function () use ($node) {
//                $this->visit($node);
//            });
//        });
    }

    public function visitStringNode(Nodes\StringNode $node)
    {
        $this->queue->push(function () use ($node) {
            $this->stack->push($node->getValue());
        });
    }

    public function visitSymbolNode(Nodes\SymbolNode $node)
    {
        $symbolName = $node->getName();

        if (is_numeric($symbolName)) {
            $this->stack->push(false !== strpos($symbolName, '.')
                ? floatval($symbolName)
                : intval($symbolName));
        } elseif (!$this->context->has($symbolName)) {
            throw new RuntimeException("Symbol \"$symbolName\" not defined");
        } else {
            $this->stack->push($this->context->get($symbolName));
        }
    }

    public function visitNativeNode(Nodes\NativeNode $node)
    {
        // TODO: Implement visitNativeNode() method.
    }

    public function visitLambdaNode(Nodes\LambdaNode $node)
    {
        // TODO: Implement visitLambdaNode() method.
    }

    public function valueOf($node)
    {
        if (is_null($node)) {
            $this->stack->push(null);
        } elseif (is_array($node)) {
            $this->stack->push(json_encode($node));
        } elseif (is_scalar($node)) {
            $this->stack->push($node);
        } elseif ($node instanceof Nodes\SymbolNode) {
            $this->queue->push(function () use ($node) {
                $this->visitSymbolNode($node);
            });
            $this->queue->push(function () {
                $this->valueOf($this->stack->shift());
            });
        } elseif ($node instanceof Nodes\StringNode) {
            $this->visitStringNode($node);
        } elseif ($node instanceof Nodes\LambdaNode) {
            $this->stack->push('[function]');
        } else {
            $this->queue->push(function () use ($node) {
                $this->visit($node);
                $this->stack->apply([$this, 'valueOf'], 1);
            });
        }
    }
}
