<?php

namespace PeacefulBit\Packet\Parser\Visitors;

use PeacefulBit\Packet\Parser\Nodes;

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
            case Nodes\ConstantNode::class:
                return $this->visitConstantNode($node);
            case Nodes\FunctionNode::class:
                return $this->visitFunctionNode($node);
            case Nodes\InvokeNode::class:
                return $this->visitInvokeNode($node);
            case Nodes\SequenceNode::class:
                return $this->visitSequenceNode($node);
            case Nodes\StringNode::class:
                return $this->visitStringNode($node);
            case Nodes\SymbolNode::class:
                return $this->visitSymbolNode($node);
            case Nodes\NativeNode::class:
                return $this->visitNativeNode($node);
            default:
                throw new \InvalidArgumentException("Unsupported type of node - $type");
        }
    }

    /**
     * @param Nodes\ConstantNode $node
     * @return mixed
     */
    abstract public function visitConstantNode(Nodes\ConstantNode $node);

    /**
     * @param Nodes\FunctionNode $node
     * @return mixed
     */
    abstract public function visitFunctionNode(Nodes\FunctionNode $node);

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

    /**
     * @param Nodes\NativeNode $node
     * @return mixed
     */
    abstract public function visitNativeNode(Nodes\NativeNode $node);
}
