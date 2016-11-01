<?php

namespace PeacefulBit\Slate\Parser\Nodes;

use function Nerd\Common\Arrays\toHeadTail;
use function Nerd\Common\Functional\tail;

use PeacefulBit\Slate\Core\Frame;

class AndExpression extends Node
{
    /**
     * @var array
     */
    private $expressions;

    /**
     * @param array $expressions
     */
    public function __construct(array $expressions)
    {
        $this->expressions = $expressions;
    }

    /**
     * @return array
     */
    public function getExpressions(): array
    {
        return $this->expressions;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return '(and ' . implode(' ', array_map('strval', $this->expressions)) . ')';
    }

    /**
     * @param Frame $frame
     * @return mixed
     */
    public function evaluate(Frame $frame)
    {
        $iter = tail(function ($rest) use (&$iter, $frame) {
            if (empty($rest)) {
                return true;
            }

            list ($head, $tail) = toHeadTail($rest);

            if (!$frame($head)) {
                return false;
            }

            return $iter($tail);
        });

        return $iter($this->getExpressions());
    }
}
