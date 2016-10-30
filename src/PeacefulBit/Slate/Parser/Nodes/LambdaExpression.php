<?php

namespace PeacefulBit\Slate\Parser\Nodes;

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
}
