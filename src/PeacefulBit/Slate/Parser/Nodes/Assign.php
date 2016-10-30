<?php

namespace PeacefulBit\Slate\Parser\Nodes;

use function Nerd\Common\Arrays\rotate;
use function Nerd\Common\Arrays\toHeadTail;
use function Nerd\Common\Strings\indent;

class Assign extends Node
{
    /**
     * @var array
     */
    private $ids;

    /**
     * @var array
     */
    private $values;

    /**
     * @param array $ids
     * @param array $values
     */
    public function __construct(array $ids, array $values)
    {
        $this->ids = $ids;
        $this->values = $values;
    }

    /**
     * @return array
     */
    public function getIds(): array
    {
        return $this->ids;
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $prefix = '(def ';
        $indentSize = strlen($prefix);

        $rotated = array_map('strval', rotate($this->getIds(), $this->getValues()));
        $chunks = array_chunk($rotated, 2);

        $groups = array_map(function ($index) use (&$chunks, $indentSize) {
            $merged = implode(' ', $chunks[$index]);
            return ($index == 0) ? $merged : indent($indentSize, $merged);
        }, array_keys($chunks));

        $suffix = ')';
        return $prefix . implode(PHP_EOL, $groups) . $suffix;
    }
}
