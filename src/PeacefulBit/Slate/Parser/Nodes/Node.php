<?php

namespace PeacefulBit\Slate\Parser\Nodes;

use PeacefulBit\Slate\Core\Evaluator;
use PeacefulBit\Slate\Core\Frame;

abstract class Node
{
    const INLINE_THRESHOLD = 20;

    abstract public function __toString();

    abstract public function evaluate(Evaluator $application, Frame $frame);

    abstract public function assign($id, $value);
}
