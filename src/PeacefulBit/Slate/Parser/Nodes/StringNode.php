<?php

namespace PeacefulBit\Slate\Parser\Nodes;

class StringNode extends Literal
{
    /**
     * @return mixed
     */
    public function __toString()
    {
        return '"' . str_replace('"', '\"', $this->getValue()) . '"';
    }
}
