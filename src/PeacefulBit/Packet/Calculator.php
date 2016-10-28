<?php

namespace PeacefulBit\Packet;

use PeacefulBit\Packet\Context\JobQueue;
use PeacefulBit\Packet\Context\Stack;
use PeacefulBit\Packet\Parser\Tokenizer;
use PeacefulBit\Packet\Context\Context;
use PeacefulBit\Packet\Visitors\NodeCalculatorVisitor;

class Calculator
{
    private $rootContext;

    public function __construct(array $native = [])
    {
        $mainContext = array_merge(
            Modules\Math\export(),
            Modules\Logic\export(),
            Modules\Relation\export(),
            Modules\Stdio\export(),
            Modules\Strings\export()
        );
        $this->rootContext = new Context(array_merge($mainContext, $native));
    }

    /**
     * @param $code
     * @return mixed
     */
    public function calculate($code)
    {
        $tokenizer  = new Tokenizer();

        $visitor    = new NodeCalculatorVisitor($this->rootContext);

        $tokens     = $tokenizer->tokenize($code);
        $tree       = $tokenizer->deflate($tokens);
        $node       = $tokenizer->convertSequenceToNode($tree);

        return $visitor->valueOf($node);
    }

    public function run($file)
    {
        $code = file_get_contents($file);
        return $this->calculate($code);
    }
}
