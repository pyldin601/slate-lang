<?php

namespace PeacefulBit\Slate\Parser\Tokens;

class IdentifierToken extends Token
{
    /**
     * @param mixed $value
     */
    public function __construct($value)
    {
        parent::__construct('id', $value);
    }
}
