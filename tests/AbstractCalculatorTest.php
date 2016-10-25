<?php

namespace tests;

use PeacefulBit\Packet\Calculator;
use PHPUnit\Framework\TestCase;

abstract class AbstractCalculatorTest extends TestCase
{
    /**
     * @var Calculator
     */
    private $calc;

    public function setUp()
    {
        $this->calc = new Calculator();
    }

    /**
     * @return Calculator
     */
    public function getCalculator()
    {
        return $this->calc;
    }
}
