<?php

namespace PeacefulBit\Slate\Parser\Evaluator;

use PeacefulBit\Slate\Exceptions\EvaluatorException;

class ProgressState implements RunState
{
    private $state;

    /**
     * ProgressState constructor.
     * @param $state
     */
    public function __construct($state)
    {
        $this->state = $state;
    }

    /**
     * Is this evaluation has next step.
     *
     * @return boolean
     */
    public function hasNext(): bool
    {
        return true;
    }

    /**
     * Get next evaluation step.
     *
     * @return RunState
     * @throws EvaluatorException
     */
    public function next(): RunState
    {
        // TODO: Implement next() method.
    }

    /**
     * Get result of evaluation.
     * @return mixed
     * @throws EvaluatorException
     */
    public function getResult()
    {
        throw new EvaluatorException("Illegal state");
    }
}
