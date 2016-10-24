<?php

namespace PeacefulBit\Packet\Parser\Nodes;

class InvokeNode extends AbstractNode
{
    /**
     * @var Node
     */
    private $function;

    /**
     * @var Node[]
     */
    private $arguments;

    /**
     * @param Node $function
     * @param Node[] $arguments
     */
    public function __construct(Node $function, array $arguments)
    {
        $this->function = $function;
        $this->arguments = $arguments;
    }

    /**
     * @return Node
     */
    public function getFunction()
    {
        return $this->function;
    }

    /**
     * @return Node[]
     */
    public function getArguments()
    {
        return $this->arguments;
    }
}
