<?php

namespace tests;

class LambdaTest extends AbstractEvaluatorTestCase
{
    public function testLambdaDeclare()
    {
        $result = $this->getEvaluator()->calculate('
            (lambda (x) (* x 2))
        ');
        $this->assertEquals('[function]', $result);
    }

    public function testLambdaAssign()
    {
        $result = $this->getEvaluator()->calculate('
            (def func (lambda (x) (* x 2)))
            (func 8)
        ');
        $this->assertEquals(16, $result);
    }
}
