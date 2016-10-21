<?php

namespace PeacefulBit\Pocket\Parser\Nodes;

class ConstDeclareNode extends AbstractNode
{
    /**
     * @var string[]
     */
    private $names;

    /**
     * @var Node[]
     */
    private $values;

    /**
     * @param string[] $names
     * @param Node[] $values
     */
    public function __construct(array $names, array $values)
    {
        $this->names = $names;
        $this->values = $values;
    }

    /**
     * @return string[]
     */
    public function getNames()
    {
        return $this->names;
    }

    /**
     * @return Node[]
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @return array
     */
    public function getCombined()
    {
        return array_combine($this->getNames(), $this->getValues());
    }
}
