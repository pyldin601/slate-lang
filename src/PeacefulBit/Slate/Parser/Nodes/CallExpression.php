<?php

namespace PeacefulBit\Slate\Parser\Nodes;

use function Nerd\Common\Strings\indent;
use PeacefulBit\Slate\Core\Evaluator;
use PeacefulBit\Slate\Core\Frame;

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
     */
    public function evaluate(Evaluator $application, Frame $frame)
    {
        $newFrame = $frame->extend($this->getArguments());
        $callee = $application->evaluate($this->getCallee(), $frame);

        return $application->evaluate($this->getCallee(), $newFrame);
    }
}
