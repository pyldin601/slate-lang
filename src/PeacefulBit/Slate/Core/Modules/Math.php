<?php

namespace PeacefulBit\Slate\Core\Modules\Math;

use function Nerd\Common\Arrays\toHeadTail;
use PeacefulBit\Packet\Exception\RuntimeException;
use PeacefulBit\Packet\Nodes\NativeNode;
use PeacefulBit\Packet\Visitors\NodeCalculatorVisitor;

function export()
{
    return [
        '+' => new NativeNode('+', function (NodeCalculatorVisitor $visitor, array $arguments) {
            return array_reduce($arguments, function ($result, $argument) use ($visitor) {
                return $result + $visitor->valueOf($argument);
            }, 0);
        }),
        '*' => new NativeNode('*', function (NodeCalculatorVisitor $visitor, array $arguments) {
            return array_reduce($arguments, function ($result, $argument) use ($visitor) {
                return $result * $visitor->valueOf($argument);
            }, 1);
        }),
        '-' => new NativeNode('-', function (NodeCalculatorVisitor $visitor, array $arguments) {
            if (sizeof($arguments) == 0) {
                throw new RuntimeException("Too few arguments");
            }
            list ($head, $tail) = toHeadTail($arguments);
            if (empty($tail)) {
                return -$visitor->valueOf($head);
            }
            return array_reduce($tail, function ($result, $argument) use ($visitor) {
                return $result - $visitor->valueOf($argument);
            }, $visitor->valueOf($head));
        }),
        '/' => new NativeNode('/', function (NodeCalculatorVisitor $visitor, array $arguments) {
            if (sizeof($arguments) == 0) {
                throw new RuntimeException("Too few arguments");
            }
            list ($head, $tail) = toHeadTail($arguments);
            if (empty($tail)) {
                return 1 / $visitor->valueOf($head);
            }
            return array_reduce($tail, function ($result, $argument) use ($visitor) {
                return $result / $visitor->valueOf($argument);
            }, $visitor->valueOf($head));
        }),
        '%' => new NativeNode('%', function (NodeCalculatorVisitor $visitor, array $arguments) {
            if (sizeof($arguments) != 2) {
                throw new RuntimeException("Modulo operation requires exactly two arguments");
            }
            list ($number, $div) = $arguments;
            return $visitor->valueOf($number) % $visitor->valueOf($div);
        }),
        'pow' => new NativeNode('pow', function (NodeCalculatorVisitor $visitor, array $arguments) {
            if (sizeof($arguments) < 2) {
                throw new RuntimeException("Function \"pow\" requires at least two arguments");
            }
            list ($head, $tail) = toHeadTail($arguments);
            return array_reduce($tail, function ($result, $argument) use ($visitor) {
                return pow($result, $visitor->valueOf($argument));
            }, $visitor->valueOf($head));
        })
    ];
}
