<?php

namespace PeacefulBit\Slate\Parser\Nodes;

use PeacefulBit\Slate\Core\Frame;

class DotIdentifier
{
    /**
     * @var string
     */
    private $path;

    /**
     * @param $name
     */
    public function __construct(string $name)
    {
        $this->path = explode('.', $name);
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode('.', $this->getPath());
    }

    /**
     * @param Frame $frame
     * @return mixed|null
     */
    public function evaluate(Frame $frame)
    {
        return $frame->getFromModule($this->getPath());
    }
}
