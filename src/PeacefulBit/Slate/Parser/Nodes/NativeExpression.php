<?php

namespace PeacefulBit\Slate\Parser\Nodes;

class NativeExpression extends Node
{
    /**
     * @var callable
     */
    private $callable;

    /**
     * @param $callable
     */
    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    /**
     * @return callable
     */
    public function getCallable(): callable
    {
        return $this->callable;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return '(native-call)';
    }
}
