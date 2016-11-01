<?php

namespace PeacefulBit\Slate\Core\Modules\Logic;

use function Nerd\Common\Arrays\all;
use function Nerd\Common\Arrays\any;

use PeacefulBit\Slate\Exceptions\EvaluatorException;
use PeacefulBit\Slate\Parser\Nodes\NativeExpression;

function export()
{
    return [
        '@' => [
            'or' => new NativeExpression(function ($eval, array $arguments) {
                $predicate = function ($item) use ($eval) {
                    return !!$eval($item);
                };
                return any($arguments, $predicate);
            }),
            'and' => new NativeExpression(function ($eval, array $arguments) {
                $predicate = function ($item) use ($eval) {
                    return !!$eval($item);
                };
                return all($arguments, $predicate);
            }),
            'not' => new NativeExpression(function ($eval, array $arguments) {
                return all($arguments, function ($argument) use ($eval) {
                    return !$eval($argument);
                });
            })
        ]
    ];
}
