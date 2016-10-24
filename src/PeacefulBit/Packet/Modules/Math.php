<?php

namespace PeacefulBit\Packet\Modules\Math;

use function Nerd\Common\Arrays\toHeadTail;
use PeacefulBit\Packet\Exception\RuntimeException;
use PeacefulBit\Packet\Nodes\NativeNode;
use PeacefulBit\Packet\Visitors\NodeCalculatorVisitor;

function export()
{
    return [
        '+' => new NativeNode('+', function (NodeCalculatorVisitor $visitor, array $arguments) {
            return array_reduce($arguments, function ($result, $argument) use ($visitor) {
                return $result + $visitor->visit($argument);
            }, 0);
        }),
        '*' => new NativeNode('*', function (NodeCalculatorVisitor $visitor, array $arguments) {
            return array_reduce($arguments, function ($result, $argument) use ($visitor) {
                return $result * $visitor->visit($argument);
            }, 1);
        }),
        '-' => new NativeNode('-', function (NodeCalculatorVisitor $visitor, array $arguments) {
            if (sizeof($arguments) == 0) {
                throw new RuntimeException("Too few arguments");
            }
            list ($head, $tail) = toHeadTail($arguments);
            if (empty($tail)) {
                return -$visitor->visit($head);
            }
            return array_reduce($tail, function ($result, $argument) use ($visitor) {
                return $result - $visitor->visit($argument);
            }, $visitor->visit($head));
        }),
        '/' => new NativeNode('/', function (NodeCalculatorVisitor $visitor, array $arguments) {
            if (sizeof($arguments) == 0) {
                throw new RuntimeException("Too few arguments");
            }
            list ($head, $tail) = toHeadTail($arguments);
            if (empty($tail)) {
                return 1 / $visitor->visit($head);
            }
            return array_reduce($tail, function ($result, $argument) use ($visitor) {
                return $result / $visitor->visit($argument);
            }, $visitor->visit($head));
        }),
        '%' => new NativeNode('%', function (NodeCalculatorVisitor $visitor, array $arguments) {
            if (sizeof($arguments) != 2) {
                throw new RuntimeException("Modulo operator accepts exactly two arguments");
            }
            list ($number, $div) = $arguments;
            return $visitor->visit($number) % $visitor->visit($div);
        }),
        'pow' => new NativeNode('pow', function (NodeCalculatorVisitor $visitor, array $arguments) {
            return array_reduce($arguments, function ($result, $argument) use ($visitor) {
                return pow($result, $visitor->visit($argument));
            }, 1);
        })
    ];
}
