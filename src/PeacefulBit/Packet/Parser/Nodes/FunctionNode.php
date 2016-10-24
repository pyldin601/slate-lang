<?php

namespace PeacefulBit\Packet\Parser\Nodes;

class FunctionNode extends AbstractNode
{
    /**
     * @var SymbolNode
     */
    private $name;

    /**
     * @var SymbolNode[]
     */
    private $arguments;

    /**
     * @var Node
     */
    private $body;

    /**
     * @param string $name
     * @param string[] $arguments
     * @param Node $body
     */
    public function __construct($name, array $arguments, Node $body)
    {
        $this->name = $name;
        $this->arguments = $arguments;
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string[]
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @return Node
     */
    public function getBody()
    {
        return $this->body;
    }
}
