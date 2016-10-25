<?php

namespace tests;

class LambdaTest extends AbstractCalculatorTest
{
    public function testLambdaDeclare()
    {
        $result = $this->getCalculator()->calculate('
            (lambda (x) (* x 2))
        ');
        $this->assertEquals('[function]', $result);
    }

    public function testLambdaAssign()
    {
        $result = $this->getCalculator()->calculate('
            (def func (lambda (x) (* x 2)))
            (func 8)
        ');
        $this->assertEquals(16, $result);
    }
}
