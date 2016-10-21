<?php

namespace PeacefulBit\Pocket\Parser\Nodes;

use PeacefulBit\Pocket\Parser\Visitors\NodeVisitor;

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
