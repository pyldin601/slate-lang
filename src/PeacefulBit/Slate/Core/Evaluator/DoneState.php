<?php

namespace PeacefulBit\Slate\Core\Evaluator;

use PeacefulBit\Slate\Exceptions\EvaluatorException;

class DoneState implements RunState
{
    private $result;

    /**
     * @param $result
     */
    public function __construct($result)
    {
        $this->result = $result;
    }

    /**
     * Is this evaluation has next step.
     * @return bool
     */
    public function hasNext(): bool
    {
        return false;
    }

    /**
     * Get next evaluation step.
     *
     * @return RunState
     * @throws EvaluatorException
     */
    public function next(): RunState
    {
        throw new EvaluatorException("Illegal state");
    }

    /**
     * Get result of evaluation.
     *
     * @return mixed
     * @throws EvaluatorException
     */
    public function getResult()
    {
        return $this->result;
    }
}
