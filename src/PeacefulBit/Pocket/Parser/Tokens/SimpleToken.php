<?php

namespace PeacefulBit\Pocket\Parser\Tokens;

abstract class SimpleToken implements Token
{
    /**
     * @return string
     */
    public function __toString()
    {
        $name = explode('\\', __CLASS__);
        return end($name);
    }
}
