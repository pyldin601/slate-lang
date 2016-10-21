<?php

namespace PeacefulBit\Pocket\Parser\Tokens;

abstract class TokenWithContent extends SimpleToken
{
    /**
     * @var mixed $content
     */
    private $content;

    /**
     * @param mixed $content
     */
    public function __construct($content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }
}
