<?php

namespace PeacefulBit\Slate\Parser\Tokens;

class StringToken extends Token
{
    /**
     * @param mixed $value
     */
    public function __construct($value)
    {
        parent::__construct('string', $value);
    }
}
