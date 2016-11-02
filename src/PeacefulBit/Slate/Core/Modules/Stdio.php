<?php

namespace PeacefulBit\Slate\Core\Modules\Stdio;

use PeacefulBit\Slate\Exceptions\EvaluatorException;
use PeacefulBit\Slate\Parser\Nodes\NativeExpression;

function export()
{
    return [
        '@' => [
            'ask' => new NativeExpression(function ($eval, array $arguments) {
                if (sizeof($arguments) != 1) {
                    throw new EvaluatorException('Function expects one argument, but none given');
                }
                echo $eval($arguments[0]);
                echo ' ';
                return trim(fgets(STDIN));
            }),
            'say' => new NativeExpression(function ($eval, array $arguments) {
                array_walk($arguments, function ($argument) use ($eval) {
                    echo $eval($argument);
                });
            }),
            'say-n' => new NativeExpression(function ($eval, array $arguments) {
                array_walk($arguments, function ($argument) use ($eval) {
                    echo $eval($argument);
                });
                echo PHP_EOL;
            }),
            'err' => new NativeExpression(function ($eval, array $arguments) {
                fwrite(STDERR, implode(' ', array_map($eval, $arguments)) . PHP_EOL);
            })
        ]
    ];
}
