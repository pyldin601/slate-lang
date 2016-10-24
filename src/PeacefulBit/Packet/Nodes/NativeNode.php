<?php

namespace PeacefulBit\Packet\Nodes;

class NativeNode extends AbstractNode
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var callable $callable
     */
    private $callable;

    /**
     * @param string $name
     * @param callable $callable
     */
    public function __construct($name, callable $callable)
    {
        $this->name = $name;
        $this->callable = $callable;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return callable
     */
    public function getCallable()
    {
        return $this->callable;
    }
}
