<?php

namespace PeacefulBit\Pocket;

use PeacefulBit\Pocket\Parser\Tokenizer;
use PeacefulBit\Pocket\Parser\Visitors\NodeCalculatorVisitor;
use PeacefulBit\Pocket\Runtime\Context\Context;

class Calculator
{
    private $rootContext;

    public function __construct(array $native = [])
    {
        $this->rootContext = new Context($native);
    }

    public function calculate($code)
    {
        $tokenizer = new Tokenizer;
        $visitor = new NodeCalculatorVisitor($this->rootContext);
        $tokens = $tokenizer->tokenize($code);
        $tree = $tokenizer->deflate($tokens);
        // ???
        return $visitor->visit($node);
    }
}
