<?php

namespace PeacefulBit\Slate\Core\Modules\Relation;

use function Nerd\Common\Arrays\toHeadTail;
use PeacefulBit\Slate\Exceptions\EvaluatorException;
use PeacefulBit\Slate\Parser\Nodes\NativeExpression;

function relationReduce($eval, $callback, array $arguments)
{
    if (empty($arguments)) {
        throw new EvaluatorException("Too few arguments");
    }
    $iter = function ($rest, $first) use (&$iter, $callback, $eval) {
        if (empty($rest)) {
            return true;
        }
        list ($head, $tail) = toHeadTail($rest);
        $visitedHead = $eval($head);
        if ($callback($first, $visitedHead)) {
            return $iter($tail, $visitedHead);
        }
        return false;
    };
    list ($head, $tail) = toHeadTail($arguments);
    return $iter($tail, $eval($head));
}

function export()
{
    return [
        '@' => [
            '>' => new NativeExpression(function ($eval, array $arguments) {
                return relationReduce($eval, function ($first, $second) {
                    return $first > $second;
                }, $arguments);
            }),
            '<' => new NativeExpression(function ($eval, array $arguments) {
                return relationReduce($eval, function ($first, $second) {
                    return $first < $second;
                }, $arguments);
            }),
            '>=' => new NativeExpression(function ($eval, array $arguments) {
                return relationReduce($eval, function ($first, $second) {
                    return $first >= $second;
                }, $arguments);
            }),
            '<=' => new NativeExpression(function ($eval, array $arguments) {
                return relationReduce($eval, function ($first, $second) {
                    return $first <= $second;
                }, $arguments);
            }),
            '=' => new NativeExpression(function ($eval, array $arguments) {
                return relationReduce($eval, function ($first, $second) {
                    return $first == $second;
                }, $arguments);
            }),
            '==' => new NativeExpression(function ($eval, array $arguments) {
                return relationReduce($eval, function ($first, $second) {
                    return $first === $second;
                }, $arguments);
            }),
            '!=' => new NativeExpression(function ($eval, array $arguments) {
                return relationReduce($eval, function ($first, $second) {
                    return $first != $second;
                }, $arguments);
            }),
            '!==' => new NativeExpression(function ($eval, array $arguments) {
                return relationReduce($eval, function ($first, $second) {
                    return $first !== $second;
                }, $arguments);
            })
        ]
    ];
}
