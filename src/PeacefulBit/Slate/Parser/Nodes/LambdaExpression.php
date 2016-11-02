<?php

namespace PeacefulBit\Slate\Parser\Nodes;

use PeacefulBit\Slate\Core\Evaluator;
use PeacefulBit\Slate\Core\Frame;

use Nerd\Common\Arrays;
use PeacefulBit\Slate\Exceptions\EvaluatorException;

class LambdaExpression extends Node implements CallableNode
{
    /**
     * @var array
     */
    private $params;

    /**
     * @var Node
     */
    private $body;

    /**
     * @param array $params
     * @param Node $body
     */
    public function __construct(array $params, Node $body)
    {
        $this->params = $params;
        $this->body = $body;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @return Node
     */
    public function getBody(): Node
    {
        return $this->body;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return '(lambda '
        . '('
        . implode(' ', array_map('strval', $this->getParams()))
        . ') '
        . strval($this->getBody())
        . ')';
    }

    /**
     * @param Frame $frame
     * @return $this
     */
    public function evaluate(Frame $frame)
    {
        return $this;
    }

    /**
     * @param Frame $frame
     * @param array $arguments
     * @return mixed
     * @throws EvaluatorException
     */
    public function call(Frame $frame, array $arguments = [])
    {
        if (sizeof($this->getParams()) != sizeof($arguments)) {
            throw new EvaluatorException("Number of arguments mismatch");
        }

        $evaluatedArguments = array_map([$frame, 'evaluate'], $arguments);

        $newFrame = array_combine($this->getParams(), $evaluatedArguments);

        return $frame->extend($newFrame)->evaluate($this->getBody());
    }
}
