<?php

namespace PeacefulBit\Slate\Parser\Nodes;

use PeacefulBit\Slate\Core\Frame;

class IfExpression extends Node
{
    private $test;
    private $consequent;
    private $alternative;

    public function __construct(Node $test, Node $consequent, Node $alternative)
    {
        $this->test = $test;
        $this->consequent = $consequent;
        $this->alternative = $alternative;
    }

    /**
     * @return Node
     */
    public function getTest(): Node
    {
        return $this->test;
    }

    /**
     * @return Node
     */
    public function getConsequent(): Node
    {
        return $this->consequent;
    }

    /**
     * @return Node
     */
    public function getAlternative(): Node
    {
        return $this->alternative;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return '(if '
        . strval($this->getTest())
        . ' '
        . strval($this->getConsequent())
        . ' '
        . strval($this->getAlternative())
        . ')';
    }

    /**
     * @param Frame $frame
     * @return mixed
     */
    public function evaluate(Frame $frame)
    {
        $test = $frame($this->test);
        return $frame($test ? $this->consequent : $this->alternative);
    }
}
