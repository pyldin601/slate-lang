<?php

namespace PeacefulBit\Slate\Parser\Nodes;

use PeacefulBit\Slate\Core\Evaluator;
use PeacefulBit\Slate\Core\Frame;

class Literal extends Node
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
     * @return mixed
     */
    public function __toString()
    {
        return strval($this->getValue());
    }

    /**
     * @param Evaluator $application
     * @param Frame $frame
     * @return mixed
     */
    public function evaluate(Evaluator $application, Frame $frame)
    {
        return $this->getValue();
    }
}
