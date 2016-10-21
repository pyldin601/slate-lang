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

    /**
     * @return string
     */
    public function __toString()
    {
        $name = explode('\\', __CLASS__);
        $short = end($name);
        return sprintf('%s(%s)', $short, $this->getContent());
    }
}
