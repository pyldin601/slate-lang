<?php

namespace PeacefulBit\Pocket\Parser\Visitors;

use PeacefulBit\Pocket\Parser\Nodes\Node;

interface NodeVisitor
{
    public function visit(Node $node);
}
