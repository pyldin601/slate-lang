<?php

namespace PeacefulBit\Slate\Parser\Nodes;

class MacroExpression extends FunctionExpression
{
    public function __toString()
    {
        return '(def-macro '
        . '('
        . strval($this->getId())
        . ' '
        . implode(' ', array_map('strval', $this->getParams()))
        . ') '
        . strval($this->getBody());
    }
}
