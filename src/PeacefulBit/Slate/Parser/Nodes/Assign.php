<?php

namespace PeacefulBit\Slate\Parser\Nodes;

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
}
