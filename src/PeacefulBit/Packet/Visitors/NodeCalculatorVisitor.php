<?php

namespace PeacefulBit\Packet\Visitors;

use PeacefulBit\Packet\Context\JobQueue;
use PeacefulBit\Packet\Context\Stack;
use PeacefulBit\Packet\Exception\RuntimeException;
use PeacefulBit\Packet\Nodes;
use PeacefulBit\Packet\Context\Context;

class NodeCalculatorVisitor implements NodeVisitor
{
    use NodeDispatchingTrait;

    /**
     * @var Context
     */
    private $context;

    /**
     * @var Stack
     */
    private $stack;

    /**
     * @var JobQueue
     */
    private $queue;

    /**
     * @param JobQueue $queue
     * @param Stack $stack
     * @param Context $context
     */
    public function __construct(JobQueue $queue, Stack $stack, Context $context)
    {
        $this->context = $context;
        $this->stack = $stack;
        $this->queue = $queue;
    }

    public function visitConstantNode(Nodes\ConstantNode $node)
    {
        $combined = $node->getCombined();
        $keys = array_keys($combined);

        array_walk($keys, function ($key) use ($node, $combined) {
            $visitor = $this->getVisitor($combined[$key]);

            $this->queue->push(function () use ($visitor, $combined, $key) {
                $this->stack->push($visitor($combined[$key]));
            });

            $this->queue->push(function () use ($key) {
                $this->context->set($key, $this->stack->shift());
            });
        });

        return null;
    }

    public function visitFunctionNode(Nodes\FunctionNode $node)
    {
        $this->context->set($node->getName(), $node);

        return null;
    }

    public function visitInvokeNode(Nodes\InvokeNode $node)
    {
        $callable = $this->getFunction($node);

        if ($callable instanceof Nodes\NativeNode) {
            return call_user_func($callable->getCallable(), $this, $node->getArguments());
        }

        if ($callable instanceof Nodes\LambdaNode) {
            return $this->callLambdaNode($callable, $node->getArguments());
        }

        throw new \RuntimeException("Symbol is not callable");
    }

    private function getFunction(Nodes\InvokeNode $node)
    {
        $function = $node->getFunction();

        if ($function instanceof Nodes\SymbolNode) {
            $name = $function->getName();
            if (!$this->context->has($name)) {
                throw new \RuntimeException("Symbol \"$name\" is not defined");
            }
            return $this->context->get($name);
        }

        if ($function instanceof Nodes\InvokeNode) {
            return $this->visitInvokeNode($function);
        }

        throw new \RuntimeException("Invalid function");
    }

    private function callLambdaNode(Nodes\LambdaNode $node, $args)
    {
        $argNames = $node->getArguments();

        if (sizeof($argNames) > sizeof($args)) {
            throw new \RuntimeException("Number of arguments mismatch");
        }

        $combined = array_combine($argNames, $this->visitListOfNodes($args));
        $childContext = $this->context->newContext($combined);
        $childVisitor = new static($this->queue, $this->stack, $childContext);

        $body = $node->getBody();
        $visitor = $childVisitor->getVisitor($body);

        return $visitor($body);
    }

    private function visitListOfNodes(array $listOfNodes)
    {
        return array_map([$this, 'visit'], $listOfNodes);
    }

    public function visitSequenceNode(Nodes\SequenceNode $node)
    {
        return array_reduce($node->getNodes(), function ($prev, $next) {
            $visitor = $this->getVisitor($next);
            return $visitor($next);
        });
    }

    public function visitStringNode(Nodes\StringNode $node)
    {
        return $node->getValue();
    }

    public function visitSymbolNode(Nodes\SymbolNode $node)
    {
        $name = $node->getName();

        if (is_numeric($name)) {
            return (false !== strpos($name, '.'))
                ? floatval($name)
                : intval($name);
        }

        if (!$this->context->has($name)) {
            throw new RuntimeException("Symbol \"$name\" not defined");
        }

        return $this->context->get($name);
    }

    public function visitNativeNode(Nodes\NativeNode $node)
    {
        // Native code could not came from source
    }

    public function valueOf($node)
    {
        if (is_null($node)) {
            return null;
        }
        if (is_array($node)) {
            return json_encode($node);
        }
        if (is_scalar($node)) {
            return $node;
        }
        if ($node instanceof Nodes\StringNode) {
            return $node->getValue();
        }
        if ($node instanceof Nodes\LambdaNode) {
            return '[function]';
        }
        $visitor = $this->getVisitor($node);
        return $this->valueOf($visitor($node));
    }

    public function visitLambdaNode(Nodes\LambdaNode $node)
    {
        return $node;
    }
}
