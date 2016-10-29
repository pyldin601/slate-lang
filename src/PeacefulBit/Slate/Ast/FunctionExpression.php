<?php

namespace PeacefulBit\Slate\Ast;

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
}
