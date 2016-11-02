<?php

namespace PeacefulBit\Slate\Core\Modules\Lists;

use PeacefulBit\Slate\Exceptions\EvaluatorException;
use PeacefulBit\Slate\Parser\Nodes\NativeExpression;

function export()
{
    return [
        '@' => [
            'list' => new NativeExpression(function ($eval, array $arguments) {
                return array_map($eval, $arguments);
            }),
            'range' => new NativeExpression(function ($eval, array $arguments) {
                switch (sizeof($arguments)) {
                    case 0:
                    case 1:
                        throw new EvaluatorException("Too few arguments");
                    case 2:
                        return range($eval($arguments[0]), $eval($arguments[1]));
                    case 3:
                        return range($eval($arguments[0]), $eval($arguments[1]), $eval($arguments[2]));
                    default:
                        throw new EvaluatorException("Too many arguments");
                }
            })
        ]
    ];
}