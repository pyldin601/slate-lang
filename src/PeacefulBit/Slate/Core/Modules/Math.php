<?php

namespace PeacefulBit\Slate\Core\Modules\Math;

use function Nerd\Common\Arrays\toHeadTail;

use PeacefulBit\Slate\Exceptions\EvaluatorException;
use PeacefulBit\Slate\Parser\Nodes\NativeExpression;

function export()
{
    return [
        'math' => [
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
            'pow' => new NativeExpression(function ($eval, array $arguments) {
                switch (sizeof($arguments)) {
                    case 0:
                    case 1:
                        throw new EvaluatorException("Function \"pow\" requires at least two arguments");
                    default:
                        list ($first, $rest) = toHeadTail($arguments);
                        return array_reduce($rest, function ($result, $argument) use ($eval) {
                            return pow($result, $eval($argument));
                        }, $eval($first));
                }
            })
        ]
    ];
}
