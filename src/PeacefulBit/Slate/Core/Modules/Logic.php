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
            'not' => new NativeExpression(function ($eval, array $arguments) {
                return all($arguments, function ($argument) use ($eval) {
                    return !$eval($argument);
                });
            })
        ]
    ];
}
