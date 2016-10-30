<?php

namespace PeacefulBit\Slate\Parser\Nodes;

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

    public function __toString()
    {
        //
    }
}
