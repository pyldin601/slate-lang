<?php

namespace PeacefulBit\Slate\Parser\Nodes;

use PeacefulBit\Slate\Core\Evaluator;
use PeacefulBit\Slate\Core\Frame;

class SequenceExpression extends Node
{
    private $expressions;

    /**
     * @param Node[] $expressions
     */
    public function __construct(array $expressions)
    {
        $this->expressions = $expressions;
    }

    /**
     * @return Node[]
     */
    public function getExpressions()
    {
        return $this->expressions;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode(PHP_EOL, array_map('strval', $this->getExpressions()));
    }

    /**
     * @param Evaluator $application
     * @param Frame $frame
     * @return mixed
     */
    public function evaluate(Evaluator $application, Frame $frame)
    {
        return array_reduce($this->getExpressions(), function ($result, $expression) use ($application, $frame) {
            return $application->evaluate($expression, $frame);
        }, null);
    }

    /**
     * @param $id
     * @param $value
     * @return SequenceExpression
     */
    public function assign($id, $value)
    {
        return new self(array_reduce($this->getExpressions(), function ($result, $expression) use ($id, $value) {
            return array_merge($result, [$expression->assign($id, $value)]);
        }, []));
    }
}
