<?php

namespace PeacefulBit\Packet\Nodes;

use PeacefulBit\Packet\Visitors\NodeVisitor;

interface Node
{
    /**
     * @param NodeVisitor $visitor
     * @return mixed
     */
    public function accept(NodeVisitor $visitor);
}
