<?php

namespace tests;

class TailRecursionTest extends AbstractCalculatorTest
{
    public function testTailRecursion()
    {
        $result = $this->getCalculator()->calculate('
            (def (iter x y)
              (if (= x y) 
                  x
                  (iter (+ x 1) y)))
            (iter 0 500)
        ');

        $this->assertEquals(500, $result);
    }
}
