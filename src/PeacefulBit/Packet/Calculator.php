<?php

namespace PeacefulBit\Packet;

use PeacefulBit\Packet\Context\JobQueue;
use PeacefulBit\Packet\Context\Stack;
use PeacefulBit\Packet\Parser\Tokenizer;
use PeacefulBit\Packet\Context\Context;
use PeacefulBit\Packet\Visitors\StackBasedNodeVisitor;

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

    /**
     * @param $code
     * @return mixed
     */
    public function calculate($code)
    {
        $tokenizer  = new Tokenizer;
        $queue      = new JobQueue();
        $stack      = new Stack();

        $visitor    = new StackBasedNodeVisitor($stack, $queue, $this->rootContext);

        $tokens     = $tokenizer->tokenize($code);
        $tree       = $tokenizer->deflate($tokens);
        $node       = $tokenizer->convertSequenceToNode($tree);

        $visitor->valueOf($node);

        $queue->run();

        return $stack->shift();
    }

    public function run($file)
    {
        $code = file_get_contents($file);
        return $this->calculate($code);
    }
}
