<?php

namespace PeacefulBit\Slate\Parser\Nodes;

use function Nerd\Common\Strings\indent;

class FunctionExpression extends LambdaExpression
{
    /**
     * @var string
     */
    private $id;

    /**
     * @param string $id
     * @param array $params
     * @param Node $body
     */
    public function __construct(string $id, array $params, Node $body)
    {
        $this->id = $id;

        parent::__construct($params, $body);
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $prefix = '(def ';

        return $prefix
        . '('
        . strval($this->getId())
        . ' '
        . implode(' ', array_map('strval', $this->getParams()))
        . ')'
        .  ' '
        . strval($this->getBody());
    }
}
