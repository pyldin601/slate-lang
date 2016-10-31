<?php

namespace PeacefulBit\Slate\Parser\Nodes;

use PeacefulBit\Slate\Core\Evaluator;
use PeacefulBit\Slate\Core\Frame;

class Assign extends Node
{
    /**
     * @var array
     */
    private $assigns;

    /**
     * @param array $assigns
     */
    public function __construct(array $assigns)
    {
        $this->assigns = $assigns;
    }

    /**
     * @return array
     */
    public function getAssigns(): array
    {
        return $this->assigns;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $prefix = '(def ';
        $suffix = ')';

        $groups = array_map(function ($assign) {
            return implode(' ', array_map('strval', $assign));
        }, $this->getAssigns());

        return $prefix . implode(' ', $groups) . $suffix;
    }

    /**
     * @param Evaluator $application
     * @param Frame $frame
     * @return null
     */
    public function evaluate(Evaluator $application, Frame $frame)
    {
        $pairs = $this->getAssigns();

        array_walk($pairs, function ($pair) use ($application, $frame) {
            $value = $application->evaluate($pair[1], $frame);
            $key = $pair[0]->getName();
            $frame->set($key, $value);
        });

        return null;
    }
}
