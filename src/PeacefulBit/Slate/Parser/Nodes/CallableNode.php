<?php

namespace PeacefulBit\Slate\Parser\Nodes;

use PeacefulBit\Slate\Core\Evaluator;
use PeacefulBit\Slate\Core\Frame;

interface CallableNode
{
    public function call(Evaluator $application, Frame $frame, array $arguments = []);
}
