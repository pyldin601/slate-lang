<?php

namespace PeacefulBit\Slate\Parser\Tokens;

class NumericToken extends Token
{
    /**
     * @param mixed $value
     */
    public function __construct($value)
    {
        parent::__construct('Numeric', $value);
    }
}
