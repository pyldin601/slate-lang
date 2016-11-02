<?php

namespace PeacefulBit\Slate\Parser\Nodes;

use PeacefulBit\Slate\Core\Frame;

abstract class Literal extends Node
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param Frame $frame
     * @return mixed
     */
    public function evaluate(Frame $frame)
    {
        return $this->getValue();
    }
}
