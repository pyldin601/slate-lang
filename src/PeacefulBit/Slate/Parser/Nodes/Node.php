<?php

namespace PeacefulBit\Slate\Parser\Nodes;

abstract class Node
{
    const INLINE_THRESHOLD = 20;

    abstract public function __toString();
}
