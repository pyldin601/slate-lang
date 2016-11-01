<?php

namespace PeacefulBit\Slate\Parser\Tokens;

class OpenBracketToken extends Token
{
    public function __construct()
    {
        parent::__construct('bracket', '(');
    }
}
