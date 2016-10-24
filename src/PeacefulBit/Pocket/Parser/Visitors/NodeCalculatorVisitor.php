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

        array_walk(array_keys($combined), function ($key) use ($node, $combined) {
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
        // TODO: Implement visitInvokeNode() method.
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
            throw new RuntimeException("Symbol \"$name\" is not defined");
        }

        return $this->visit($this->context->get($name));
    }
}