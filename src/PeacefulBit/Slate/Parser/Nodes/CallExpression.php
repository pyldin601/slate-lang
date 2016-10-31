<?php

namespace PeacefulBit\Slate\Parser\Nodes;

use function Nerd\Common\Strings\indent;
use PeacefulBit\Slate\Core\Evaluator;
use PeacefulBit\Slate\Core\Frame;
use PeacefulBit\Slate\Exceptions\EvaluatorException;

class CallExpression extends Node
{
    /**
     * @var Node
     */
    private $callee;

    /**
     * @var Node[]
     */
    private $arguments;

    /**
     * @param Node $callee
     * @param Node[] $arguments
     */
    public function __construct(Node $callee, array $arguments)
    {
        $this->callee = $callee;
        $this->arguments = $arguments;
    }

    /**
     * @return Node
     */
    public function getCallee(): Node
    {
        return $this->callee;
    }

    /**
     * @return Node[]
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function __toString()
    {
        $prefix = '(' . strval($this->getCallee()) . ' ';
        $suffix = ')';

        $arguments = array_map('strval', $this->getArguments());

        return $prefix . implode(' ', $arguments) . $suffix;
    }

    /**
     * @param Evaluator $application
     * @param Frame $frame
     * @return mixed
     * @throws EvaluatorException
     */
    public function evaluate(Evaluator $application, Frame $frame)
    {
        $callable = $application->evaluate($this->getCallee(), $frame);

        if (!$callable instanceof CallableNode) {
            throw new EvaluatorException("Callee must be Callable");
        }

        $result = $callable->call($this->getArguments());

        var_dump($result);

        $newFrame = $frame->extend($this->getArguments());

        return $application->evaluate($this->getCallee(), $newFrame);
    }

    /**
     * @param $id
     * @param $value
     * @return CallExpression
     */
    public function assign($id, $value)
    {
        $callee = $this->getCallee()->assign($id, $value);
        $arguments = array_map(function ($argument) use ($id, $value) {
            return $argument->assign($id, $value);
        }, $this->getArguments());
        return new self($callee, $arguments);
    }
}
