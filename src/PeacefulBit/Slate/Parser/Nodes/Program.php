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
    private function getBody()
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
     * @param Evaluator $application
     * @param Frame $frame
     * @return mixed
     */
    public function evaluate(Evaluator $application, Frame $frame)
    {
        return array_reduce($this->getBody(), function ($result, $expression) use ($application, $frame) {
            return $application->evaluate($expression, $frame);
        }, null);
    }
}
