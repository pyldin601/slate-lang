<?php

namespace PeacefulBit\LispMachine\VM\Core\Math;

function export()
{
    return [
        '+' => function ($env, array $arguments) {
            return array_reduce($arguments, function ($env, $argument) {
                //
            }, $env);
        }
    ];
}