<?php

namespace PeacefulBit\Packet\Nodes;

use PeacefulBit\Packet\Visitors\NodeVisitor;

abstract class AbstractNode implements Node
{
    /**
     * @param NodeVisitor $visitor
     * @return mixed
     */
    public function accept(NodeVisitor $visitor)
    {
        return $visitor->visit($this);
    }
}
