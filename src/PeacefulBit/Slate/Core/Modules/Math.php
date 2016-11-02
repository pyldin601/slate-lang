<?php

namespace PeacefulBit\Slate\Core\Modules\Math;

use function Nerd\Common\Arrays\toHeadTail;

use PeacefulBit\Slate\Exceptions\EvaluatorException;
use PeacefulBit\Slate\Parser\Nodes\NativeExpression;

function export()
{
    return [
        '@' => [
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
