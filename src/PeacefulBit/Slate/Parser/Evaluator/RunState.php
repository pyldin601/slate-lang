<?php

namespace PeacefulBit\Slate\Parser\Evaluator;

use PeacefulBit\Slate\Exceptions\EvaluatorException;

interface RunState
{
    /**
     * Is this evaluation has next step.
     *
     * @return boolean
     */
    public function hasNext(): bool;

    /**
     * Get next evaluation step.
     *
     * @return RunState
     * @throws EvaluatorException
     */
    public function next(): RunState;

    /**
     * Get result of evaluation.
     *
     * @return mixed
     * @throws EvaluatorException
     */
    public function getResult();
}
