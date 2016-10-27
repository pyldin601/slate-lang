<?php

namespace PeacefulBit\Packet\Context;

use PeacefulBit\Packet\Exception\RuntimeException;

class Stack
{
    private $stack = [];

    public function push(...$values)
    {
        $this->stack = array_merge($this->stack, $values);

        return $this;
    }

    public function apply($fn, $argCount = 0, $pushResult = true)
    {
        $arguments = $this->shiftGroup($argCount);
        $result = $fn(...$arguments);

        if ($pushResult) {
            $this->push($result);
        }

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
        if ($number > $this->size()) {
            throw new RuntimeException("Not enough data in stack");
        }
        $fetched = array_slice($this->stack, $this->size() - $number);
        $this->stack = array_slice($this->stack, 0, $this->size() - $number);

        return $fetched;
    }

    /**
     * @return int
     */
    public function size()
    {
        return sizeof($this->stack);
    }
}
