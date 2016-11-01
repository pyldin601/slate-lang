<?php

namespace PeacefulBit\Slate\Parser\Tokens;

class CloseBracketToken extends Token
{
    public function __construct()
    {
        parent::__construct('bracket', ')');
    }
}
