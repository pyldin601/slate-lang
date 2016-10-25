<?php

namespace tests;

class TailRec extends AbstractCalculatorTest
{
    public function testTailRecursion()
    {
        $this->getCalculator()->calculate('
        (def (iter x y)
          (if (= x y) 
              x
              (iter (+ x 1) y)))
        (iter 0 1000)
        ');
    }
}
