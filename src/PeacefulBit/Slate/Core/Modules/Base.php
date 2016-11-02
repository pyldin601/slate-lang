<?php

namespace PeacefulBit\Slate\Core\Modules\Base;

use function Nerd\Common\Arrays\all;
use function Nerd\Common\Arrays\any;

use function Nerd\Common\Arrays\toHeadTail;
use PeacefulBit\Slate\Exceptions\EvaluatorException;
use PeacefulBit\Slate\Parser\Nodes\NativeExpression;

function export()
{
    return [
        '@' => [
            'not' => new NativeExpression(function ($eval, array $arguments) {
                return all($arguments, function ($argument) use ($eval) {
                    return !$eval($argument);
                });
            }),
            '+' => new NativeExpression(function ($eval, array $arguments) {
                return array_reduce($arguments, function ($result, $argument) use ($eval) {
                    return $result + $eval($argument);
                }, 0);
            }),
            '*' => new NativeExpression(function ($eval, array $arguments) {
                return array_reduce($arguments, function ($result, $argument) use ($eval) {
                    return $result * $eval($argument);
                }, 1);
            }),
            '-' => new NativeExpression(function ($eval, array $arguments) {
                switch (sizeof($arguments)) {
                    case 0:
                        throw new EvaluatorException("Too few arguments");
                    case 1:
                        return $eval($arguments[0]);
                    default:
                        list ($first, $rest) = toHeadTail($arguments);
                        return array_reduce($rest, function ($result, $argument) use ($eval) {
                            return $result - $eval($argument);
                        }, $eval($first));
                }
            }),
            '/' => new NativeExpression(function ($eval, array $arguments) {
                switch (sizeof($arguments)) {
                    case 0:
                        throw new EvaluatorException("Too few arguments");
                    case 1:
                        return 1 / $eval($arguments[0]);
                    default:
                        list ($first, $rest) = toHeadTail($arguments);
                        return array_reduce($rest, function ($result, $argument) use ($eval) {
                            return $result / $eval($argument);
                        }, $eval($first));
                }
            }),
            '%' => new NativeExpression(function ($eval, array $arguments) {
                if (sizeof($arguments) != 2) {
                    throw new EvaluatorException("Modulo operation requires exactly two arguments");
                }
                list ($number, $div) = $arguments;
                return $eval($number) % $eval($div);
            }),
        ]
    ];
}
