<?php

namespace tests;

use PeacefulBit\Slate\Core\Evaluator;
use PHPUnit\Framework\TestCase;

abstract class AbstractEvaluatorTestCase extends TestCase
{
    /**
     * @var Evaluator
     */
    private $eval;

    public function setUp()
    {
        $this->eval = new Evaluator();
    }

    /**
     * @param string $code
     * @return mixed
     */
    public function evaluate(string $code)
    {
        return $this->eval->evaluate($code);
    }
}
