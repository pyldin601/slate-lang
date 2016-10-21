<?php

namespace PeacefulBit\Pocket\Parser\Visitors;

use PeacefulBit\Pocket\Parser\Nodes;

trait NodeDispatchingTrait
{
    /**
     * @param Nodes\Node $node
     * @return mixed
     */
    public function visit(Nodes\Node $node)
    {
        $type = get_class($node);
        switch ($type) {
            case Nodes\ConstDeclareNode::class:
                return $this->visitConstDeclareNode($node);
            case Nodes\FunctionDeclareNode::class:
                return $this->visitFunctionDeclareNode($node);
            case Nodes\InvokeNode::class:
                return $this->visitInvokeNode($node);
            case Nodes\SequenceNode::class:
                return $this->visitSequenceNode($node);
            case Nodes\StringNode::class:
                return $this->visitStringNode($node);
            case Nodes\SymbolNode::class:
                return $this->visitSymbolNode($node);
            default:
                throw new \InvalidArgumentException("Unsupported type of node - $type");
        }
    }

    /**
     * @param Nodes\ConstDeclareNode $node
     * @return mixed
     */
    abstract public function visitConstDeclareNode(Nodes\ConstDeclareNode $node);

    /**
     * @param Nodes\FunctionDeclareNode $node
     * @return mixed
     */
    abstract public function visitFunctionDeclareNode(Nodes\FunctionDeclareNode $node);

    /**
     * @param Nodes\InvokeNode $node
     * @return mixed
     */
    abstract public function visitInvokeNode(Nodes\InvokeNode $node);

    /**
     * @param Nodes\SequenceNode $node
     * @return mixed
     */
    abstract public function visitSequenceNode(Nodes\SequenceNode $node);

    /**
     * @param Nodes\StringNode $node
     * @return mixed
     */
    abstract public function visitStringNode(Nodes\StringNode $node);

    /**
     * @param Nodes\SymbolNode $node
     * @return mixed
     */
    abstract public function visitSymbolNode(Nodes\SymbolNode $node);
}
