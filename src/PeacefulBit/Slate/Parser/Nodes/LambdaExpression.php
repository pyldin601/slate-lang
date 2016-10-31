<?php

namespace PeacefulBit\Slate\Parser\Nodes;

use PeacefulBit\Slate\Core\Evaluator;
use PeacefulBit\Slate\Core\Frame;

class LambdaExpression extends Node
{
    /**
     * @var array
     */
    private $params;

    /**
     * @var Node
     */
    private $body;

    /**
     * @param array $params
     * @param Node $body
     */
    public function __construct(array $params, Node $body)
    {
        $this->params = $params;
        $this->body = $body;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @return Node
     */
    public function getBody(): Node
    {
        return $this->body;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return '(lambda '
        . '('
        . implode(' ', array_map('strval', $this->getParams()))
        . ') '
        . strval($this->getBody());
    }

    /**
     * @param Evaluator $application
     * @param Frame $frame
     * @return $this
     */
    public function evaluate(Evaluator $application, Frame $frame)
    {
        return $this;
    }
}
