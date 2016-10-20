<?php

namespace PeacefulBit\LispMachine\VM\Core\Logical;

use function PeacefulBit\LispMachine\VM\evaluateExpression;
use PeacefulBit\LispMachine\VM\VMException;

function export()
{
    return [
        'or' => function ($env, array $arguments) {
            $iter = function ($rest) use (&$iter, $env) {
                if (empty($rest)) {
                    return false;
                }
                $head = $rest[0];
                $tail = array_slice($rest, 1);
                return evaluateExpression($env, $head) || $iter($tail);
            };
            return $iter($arguments);
        },
        'and' => function ($env, array $arguments) {
            $iter = function ($rest) use (&$iter, $env) {
                if (empty($rest)) {
                    return true;
                }
                $head = $rest[0];
                $tail = array_slice($rest, 1);
                return evaluateExpression($env, $head) && $iter($tail);
            };
            return $iter($arguments);
        },
        'not' => function ($env, array $arguments) {
            if (sizeof($arguments) != 1) {
                throw new VMException("Logical 'not' accepts only one argument");
            }
            return !evaluateExpression($env, $arguments[0]);
        }
    ];
}
