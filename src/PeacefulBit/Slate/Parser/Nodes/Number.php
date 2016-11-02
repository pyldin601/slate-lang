<?php

namespace PeacefulBit\Slate\Parser\Nodes;

class Number extends Literal
{
    /**
     * @return mixed
     */
    public function __toString()
    {
        return strval($this->getValue());
    }
}
