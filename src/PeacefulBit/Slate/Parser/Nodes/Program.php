<?php

namespace PeacefulBit\Slate\Parser\Nodes;

use PeacefulBit\Slate\Core\Evaluator;
use PeacefulBit\Slate\Core\Frame;

class Program extends Node
{
    private $body;

    /**
     * @param $body
     */
    public function __construct($body)
    {
        $this->body = $body;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode(PHP_EOL, array_map('strval', $this->getBody()));
    }

    /**
     * @param Frame $frame
     * @return mixed
     */
    public function evaluate(Frame $frame)
    {
        return array_reduce($this->getBody(), function ($result, $expression) use ($frame) {
            return $frame->evaluate($expression);
        }, null);
    }
}
