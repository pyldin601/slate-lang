<?php

namespace PeacefulBit\Packet\Modules\Stdio;

use PeacefulBit\Packet\Exception\RuntimeException;
use PeacefulBit\Packet\Nodes\NativeNode;
use PeacefulBit\Packet\Nodes\StringNode;
use PeacefulBit\Packet\Visitors\NodeCalculatorVisitor;

function export()
{
    return [
        'input' => new NativeNode('input', function (NodeCalculatorVisitor $visitor, array $arguments) {
            if (empty($arguments)) {
                throw new RuntimeException('Function expects one argument, but none given');
            }
            echo $visitor->valueOf($arguments[0]);
            echo ' ';
            return new StringNode(fgets(STDIN));
        }),
        'print' => new NativeNode('print', function (NodeCalculatorVisitor $visitor, array $arguments) {
            array_walk($arguments, function ($argument) use ($visitor) {
                echo $visitor->valueOf($argument);
            });
        })
    ];
}
