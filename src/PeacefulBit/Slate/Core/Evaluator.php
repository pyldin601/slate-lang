<?php

namespace PeacefulBit\Slate\Core;

use PeacefulBit\Slate\Parser\Nodes;

class Evaluator
{
    /**
     * @var array
     */
    private $modules;

    /**
     * @param array $modules
     */
    public function __construct(array $modules = [])
    {
        $this->modules = $modules;
    }

    /**
     * @param Nodes\Node $node
     * @param Frame $frame
     * @return mixed
     */
    public function evaluate(Nodes\Node $node, Frame $frame)
    {
        return $node->evaluate($this, $frame);
    }
}
