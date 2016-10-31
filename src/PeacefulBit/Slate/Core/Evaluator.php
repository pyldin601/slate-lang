<?php

namespace PeacefulBit\Slate\Core;

use function Nerd\Common\Functional\tail;

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
        $iter = tail(function ($node) use (&$iter, $frame) {
            if ($node instanceof Nodes\CallExpression) {
                return $iter($node->evaluate($this, $frame));
            }
            return $node;
        });

        return $iter($node->evaluate($this, $frame));
    }

    /**
     * @param Nodes\Node $node
     * @return mixed
     */
    public function valueOf(Nodes\Node $node)
    {
        $frame = new Frame();

        $iter = tail(function ($node, Frame $frame) use (&$iter) {
            if ($node instanceof Nodes\Node) {
                return $iter($node->evaluate($this, $frame), $frame);
            }
            return $node;
        });

        return $iter($node, $frame);
    }
}
