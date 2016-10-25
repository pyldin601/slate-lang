<?php

namespace PeacefulBit\Packet\Nodes;

class FunctionNode extends LambdaNode
{
    /**
     * @var SymbolNode
     */
    private $name;

    /**
     * @param string $name
     * @param string[] $arguments
     * @param Node $body
     */
    public function __construct($name, array $arguments, Node $body)
    {
        parent::__construct($arguments, $body);
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
