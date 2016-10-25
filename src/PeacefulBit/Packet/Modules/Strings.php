<?php

namespace PeacefulBit\Packet\Modules\Strings;

use PeacefulBit\Packet\Exception\RuntimeException;
use PeacefulBit\Packet\Nodes\NativeNode;
use PeacefulBit\Packet\Nodes\StringNode;
use PeacefulBit\Packet\Visitors\NodeCalculatorVisitor;

function export()
{
    return [
        'concat' => new NativeNode('input', function (NodeCalculatorVisitor $visitor, array $arguments) {
            return array_reduce($arguments, function ($acc, $arg) use ($visitor) {
                return $acc . $visitor->valueOf($arg);
            }, '');
        })
    ];
}
