<?php

namespace PeacefulBit\Packet\Modules\Logic;

use function Nerd\Common\Arrays\all;
use function Nerd\Common\Arrays\toHeadTail;
use PeacefulBit\Packet\Exception\RuntimeException;
use PeacefulBit\Packet\Nodes\NativeNode;
use PeacefulBit\Packet\Visitors\NodeCalculatorVisitor;

function export()
{
    return [
        'or' => new NativeNode('or', function (NodeCalculatorVisitor $visitor, array $arguments) {
            $iter = function ($rest) use (&$iter, $visitor) {
                if (empty($rest)) {
                    return false;
                }
                list ($head, $tail) = toHeadTail($rest);
                $exactVisitor = $visitor->getVisitor($head);
                return $exactVisitor($head) || $iter($tail);
            };
            return $iter($arguments);
        }),
        'and' => new NativeNode('and', function (NodeCalculatorVisitor $visitor, array $arguments) {
            $iter = function ($rest) use (&$iter, $visitor) {
                if (empty($rest)) {
                    return true;
                }
                list ($head, $tail) = toHeadTail($rest);
                return $visitor->visit($head) && $iter($tail);
            };
            return $iter($arguments);
        }),
        'not' => new NativeNode('not', function (NodeCalculatorVisitor $visitor, array $arguments) {
            return all($arguments, function ($argument) use ($visitor) {
                return !$visitor->visit($argument);
            });
        }),
        'if' => new NativeNode('if', function (NodeCalculatorVisitor $visitor, array $arguments) {
            if (sizeof($arguments) != 3) {
                throw new RuntimeException("Function 'if' accepts only three arguments");
            }
            list ($expr, $onTrue, $onFalse) = $arguments;
            $expressionVisitor = $visitor->getVisitor($expr);
            $trueVisitor = $visitor->getVisitor($onTrue);
            $falseVisitor = $visitor->getVisitor($onFalse);
            return $expressionVisitor($expr)
                ? $trueVisitor($onTrue)
                : $falseVisitor($onFalse);
        }),
        'unless' => new NativeNode('unless', function (NodeCalculatorVisitor $visitor, array $arguments) {
            if (sizeof($arguments) != 3) {
                throw new RuntimeException("Function 'unless' accepts only three arguments");
            }
            list ($expr, $onTrue, $onFalse) = $arguments;
            return $visitor->visit($expr)
                ? $visitor->visit($onFalse)
                : $visitor->visit($onTrue);
        })
    ];
}
