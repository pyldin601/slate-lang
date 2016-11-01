<?php

namespace PeacefulBit\Slate\Parser\Nodes;

use function Nerd\Common\Strings\toString;

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

    /**
     * @return string
     */
    public function __toString()
    {
        $prefix = '(' . strval($this->getCallee()) . ' ';
        $suffix = ')';

        $arguments = array_map('strval', $this->getArguments());

        return $prefix . implode(' ', $arguments) . $suffix;
    }

    /**
     * @param Frame $frame
     * @return mixed
     * @throws EvaluatorException
     */
    public function evaluate(Frame $frame)
    {
        $callee = $frame->evaluate($this->getCallee());

        if (!$callee instanceof CallableNode) {
            throw new EvaluatorException(sprintf("%s is not callable", toString($callee)));
        }

        return $callee->call($frame, $this->getArguments());
    }

    /**
     * @param Frame $frame
     * @return CallExpression
     */
    public function close(Frame $frame)
    {
//        $closedArgs = array_map(function (Node $argument) use ($frame) {
//            return $argument instanceof Identifier ? $argument->evaluate($frame) : $argument;
//        }, $this->getArguments());

        return new self($this->getCallee(), $this->getArguments());
    }
}
