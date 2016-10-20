<?php

namespace PeacefulBit\LispMachine\VM\Core\Math;

use function PeacefulBit\LispMachine\VM\evaluateExpression;

function export()
{
    return [
        '+' => function ($env, array $arguments) {
            $sum = array_reduce($arguments, function ($result, $argument) use ($env) {
                return $result + evaluateExpression($env, $argument);
            }, 0);
            return [$env, $sum];
        }
    ];
}
