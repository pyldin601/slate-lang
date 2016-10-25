<?php

namespace PeacefulBit\Packet\Nodes;

class LambdaNode extends AbstractNode
{
    private $arguments;
    private $body;

    /**
     * LambdaNode constructor.
     * @param $arguments
     * @param $body
     */
    public function __construct(array $arguments, Node $body)
    {
        $this->arguments = $arguments;
        $this->body = $body;
    }

    /**
     * @return array
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
