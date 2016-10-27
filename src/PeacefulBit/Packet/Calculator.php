<?php

namespace PeacefulBit\Packet;

use PeacefulBit\Packet\Context\JobQueue;
use PeacefulBit\Packet\Context\Stack;
use PeacefulBit\Packet\Parser\Tokenizer;
use PeacefulBit\Packet\Visitors\NodeCalculatorVisitor;
use PeacefulBit\Packet\Context\Context;

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
            Modules\Strings\export(),
            $native
        );
        $this->rootContext = new Context($mainContext);
    }

    public function calculate($code)
    {
        $tokenizer = new Tokenizer;
        $queue = new JobQueue();
        $stack = new Stack();
        $visitor = new NodeCalculatorVisitor($queue, $stack, $this->rootContext);
        $tokens = $tokenizer->tokenize($code);
        $tree = $tokenizer->deflate($tokens);
        $node = $tokenizer->convertSequenceToNode($tree);
        return $visitor->valueOf($node);
    }

    public function run($file)
    {
        $code = file_get_contents($file);
        return $this->calculate($code);
    }
}
