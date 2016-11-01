<?php

namespace PeacefulBit\Slate\Parser\Nodes;

use PeacefulBit\Slate\Core\Frame;

class ApplicativeCallExpression extends NormalCallExpression
{
    /**
     * @param Frame $frame
     * @return ApplicativeCallExpression
     */
    public function close(Frame $frame)
    {
        $evaluatedArguments = array_map([$frame, 'evaluate'], $this->getArguments());
        return new self($this->getCallee(), $evaluatedArguments);
    }
}
