<?php

namespace PeacefulBit\Packet\Parser\Visitors;

use PeacefulBit\Packet\Parser\Nodes\Node;

interface NodeVisitor
{
    public function visit(Node $node);
}
