<?php

namespace PeacefulBit\Pocket\Parser\Nodes;

use PeacefulBit\Pocket\Parser\Visitors\NodeVisitor;

interface Node
{
    /**
     * @param NodeVisitor $visitor
     * @return mixed
     */
    public function accept(NodeVisitor $visitor);
}
