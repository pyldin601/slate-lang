<?php

namespace PeacefulBit\Pocket\Parser\Visitors;

use PeacefulBit\Pocket\Parser\Nodes;

class NodePrinterVisitor implements NodeVisitor
{
    use NodeDispatchingTrait;

    public function visitConstDeclareNode(Nodes\ConstantNode $node)
    {
        $names = $node->getNames();
        $values = $node->getValues();

        $zippedArguments = array_map(function ($name, $value) {
            return implode(' ', [$name, $this->visit($value)]);
        }, $names, $values);

        return sprintf('(def %s)', implode(' ', $zippedArguments));
    }

    public function visitFunctionDeclareNode(Nodes\FunctionNode $node)
    {
        $name = $node->getName();
        $args = implode(' ', $node->getArguments());
        $body = $this->visit($node->getBody());

        return sprintf('(def (%s %s) %s)', $name, $args, $body);
    }

    public function visitInvokeNode(Nodes\InvokeNode $node)
    {
        $all = array_merge([$node->getFunction()], $node->getArguments());
        $args = implode(' ', array_map([$this, 'visit'], $all));

        return sprintf('(%s)', $args);
    }

    public function visitSequenceNode(Nodes\SequenceNode $node)
    {
        return implode(' ', array_map([$this, 'visit'], $node->getNodes()));
    }

    public function visitStringNode(Nodes\StringNode $node)
    {
        return sprintf('"%s"', str_replace('"', '\\"', $node->getValue()));
    }

    public function visitSymbolNode(Nodes\SymbolNode $node)
    {
        return $node->getName();
    }

    public function visitNativeNode(Nodes\NativeNode $node)
    {
        return '(native)';
    }
}
