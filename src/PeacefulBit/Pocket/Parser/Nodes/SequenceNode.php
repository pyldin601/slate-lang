<?php

namespace PeacefulBit\Pocket\Parser\Nodes;

class SequenceNode extends AbstractNode
{
    /**
     * @var Node[]
     */
    private $nodes;

    /**
     * @param Node[] $nodes
     */
    public function __construct($nodes)
    {
        $this->nodes = $nodes;
    }

    /**
     * @return Node[]
     */
    public function getNodes()
    {
        return $this->nodes;
    }
}
