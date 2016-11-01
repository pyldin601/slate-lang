<?php

namespace PeacefulBit\Slate\Parser\Nodes;

use PeacefulBit\Slate\Core\Frame;

abstract class Node implements NodeInterface
{
    const INLINE_THRESHOLD = 20;

    abstract public function __toString();

    abstract public function evaluate(Frame $frame);
}
