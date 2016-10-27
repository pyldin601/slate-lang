<?php

namespace PeacefulBit\Packet\Context;

use function Nerd\Common\Arrays\append;
use function Nerd\Common\Functional\tail;

use PeacefulBit\Packet\Exception\RuntimeException;

class Stack
{
    private $stack = [];

    public function push(...$values)
    {
        $this->stack = array_merge($this->stack, $values);

        return $this;
    }

    public function apply($fn, $argCount = 0)
    {
        $arguments = $this->shiftGroup($argCount);
        $this->push($fn(...$arguments));

        return $this;
    }

    public function shift()
    {
        if (empty($this->stack)) {
            throw new RuntimeException("Stack is empty");
        }

        return array_pop($this->stack);
    }

    public function shiftGroup($number)
    {
        $iter = tail(function ($left, $acc) use (&$iter) {
            if ($left == 0) {
                return array_reverse($acc);
            }
            return $iter($left - 1, append($acc, $this->shift()));
        });

        return $iter($number, []);
    }

    /**
     * @return int
     */
    public function size()
    {
        return sizeof($this->stack);
    }
}
