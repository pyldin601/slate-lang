<?php

namespace PeacefulBit\Slate\Parser\Nodes;

use PeacefulBit\Slate\Core\Frame;
use PeacefulBit\Slate\Exceptions\EvaluatorException;

interface NodeInterface
{
    /**
     * @param Frame $frame
     * @return mixed
     * @throws EvaluatorException
     */
    public function evaluate(Frame $frame);
}
