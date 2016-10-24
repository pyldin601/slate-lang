<?php

namespace PeacefulBit\Packet\Visitors;

use PeacefulBit\Packet\Nodes\Node;

interface NodeVisitor
{
    public function visit(Node $node);
}
