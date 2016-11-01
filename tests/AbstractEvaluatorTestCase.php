<?php

namespace tests;

use PeacefulBit\Slate\Core\Evaluator;
use PeacefulBit\Slate\Parser\Nodes\NativeExpression;
use PHPUnit\Framework\TestCase;

abstract class AbstractEvaluatorTestCase extends TestCase
{
    /**
     * @var Evaluator
     */
    private $eval;

    public function setUp()
    {
        $this->eval = new Evaluator(['test' => [
            '?' => new NativeExpression(function ($eval, array $arguments) {
                $evaluatedArguments = array_map($eval, $arguments);
                return implode(' ? ', $evaluatedArguments);
            })
        ]]);
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
