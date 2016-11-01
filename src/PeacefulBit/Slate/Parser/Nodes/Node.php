<?php

namespace PeacefulBit\Slate\Parser\Nodes;

use PeacefulBit\Slate\Core\Frame;

abstract class Node implements NodeInterface
{
    const INLINE_THRESHOLD = 20;

    /**
     * @return string
     */
    abstract public function __toString();

    /**
     * @param Frame $frame
     * @return mixed
     */
    abstract public function evaluate(Frame $frame);
}
