<?php

namespace PeacefulBit\Slate\Parser\Tokens;

class DotIdentifierToken extends Token
{
    /**
     * @param mixed $value
     */
    public function __construct($value)
    {
        parent::__construct('dot_id', $value);
    }
}
