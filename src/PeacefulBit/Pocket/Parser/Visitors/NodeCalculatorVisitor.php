<?php

namespace PeacefulBit\Pocket\Parser\Visitors;

use PeacefulBit\Pocket\Exception\RuntimeException;
use PeacefulBit\Pocket\Parser\Nodes;
use PeacefulBit\Pocket\Runtime\Context\Context;

class NodeCalculatorVisitor implements NodeVisitor
{
    use NodeDispatchingTrait;

    private $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function visitConstantNode(Nodes\ConstantNode $node)
    {
        $combined = $node->getCombined();
        $keys = array_keys($combined);

        array_walk($keys, function ($key) use ($node, $combined) {
            $value = $this->visit($combined[$key]);
            $this->context->set($key, $value);
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
        $callable = $this->getFunction($node->getFunction());

        if ($callable instanceof Nodes\NativeNode) {
            return call_user_func($callable->getCallable(), $this, $node->getArguments());
        }

        if ($callable instanceof Nodes\FunctionNode) {
            return $this->callFunctionNode($callable, $node->getArguments());
        }

        throw new \RuntimeException("Symbol is not callable");
    }

    private function getFunction(Nodes\Node $function)
    {
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

    private function callFunctionNode(Nodes\FunctionNode $node, $args)
    {
        $argNames = $node->getArguments();

        if (sizeof($argNames) > sizeof($args)) {
            throw new \RuntimeException("Number of arguments mismatch");
        }

        $combined = array_combine($argNames, $args);
        $childContext = $this->context->newContext($combined);
        $childVisitor = new static($childContext);

        return $childVisitor->visit($node->getBody());
    }

    public function visitSequenceNode(Nodes\SequenceNode $node)
    {
        return array_reduce($node->getNodes(), function ($prev, $next) {
            return $this->visit($next);
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
}
