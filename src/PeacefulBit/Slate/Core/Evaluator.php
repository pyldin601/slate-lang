<?php

namespace PeacefulBit\Slate\Core;

use PeacefulBit\Slate\Parser\Parser;
use PeacefulBit\Slate\Parser\Tokenizer;

class Evaluator
{
    /**
     * @var array
     */
    private $modules;

    /**
     * @param array $modules
     */
    public function __construct(array $modules = [])
    {
        $this->modules = $modules;
    }

    /**
     * @param string $code
     * @return mixed
     */
    public function evaluate(string $code)
    {
        $tokenizer = new Tokenizer();
        $tokens = $tokenizer->tokenize($code);

        $parser = new Parser();
        $ast = $parser->parse($tokens);

        $frame = new Frame($this, $this->modules);

        return $frame->valueOf($ast);
    }
}
