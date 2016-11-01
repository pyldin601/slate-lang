<?php

namespace PeacefulBit\Slate\Parser\Nodes;

use PeacefulBit\Slate\Core\Evaluator;
use PeacefulBit\Slate\Core\Frame;

class Identifier extends Node
{
    /**
     * @var string
     */
    private $name;

    /**
     * @param $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @param Frame $frame
     * @return mixed|null
     */
    public function evaluate(Frame $frame)
    {
        return $frame->get($this->getName());
    }
}
