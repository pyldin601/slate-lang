<?php

namespace tests;

use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    public function testEmptyExpression()
    {
        $expression = "";
        $result = \Lisp\VM\Parser\toLexemes($expression);
        $this->assertEmpty($result);
    }
}
