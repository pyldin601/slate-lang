<?php

namespace PeacefulBit\Packet\Parser\Nodes;

use PeacefulBit\Packet\Parser\Visitors\NodeVisitor;

interface Node
{
    /**
     * @param NodeVisitor $visitor
     * @return mixed
     */
    public function accept(NodeVisitor $visitor);
}
