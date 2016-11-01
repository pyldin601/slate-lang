<?php

namespace PeacefulBit\Slate\Parser\Nodes;

use PeacefulBit\Slate\Core\Frame;

class NativeExpression implements CallableNode
{
    private $callable;

    /**
     * @param $callable
     */
    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    /**
     * @param Frame $frame
     * @param array $arguments
     * @return mixed
     */
    public function call(Frame $frame, array $arguments = [])
    {
        $eval = function ($node) use ($frame) {
            return $frame->evaluate($node);
        };
        return call_user_func($this->callable, $eval, $arguments);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return '(native call)';
    }
}
