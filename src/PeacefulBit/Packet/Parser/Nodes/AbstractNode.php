<?php

namespace PeacefulBit\Packet\Parser\Nodes;

use PeacefulBit\Packet\Parser\Visitors\NodeVisitor;

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
