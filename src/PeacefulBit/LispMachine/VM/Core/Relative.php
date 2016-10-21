<?php

namespace PeacefulBit\LispMachine\VM\Core\Relative;

use function PeacefulBit\LispMachine\Calculus\evaluate;

use PeacefulBit\LispMachine\VM\VMException;

function export()
{
    return [
        '>' => function ($env, $arguments) {
            return reduce($env, function ($first, $second) {
                return $first > $second;
            }, $arguments);
        },
        '<' => function ($env, $arguments) {
            return reduce($env, function ($first, $second) {
                return $first < $second;
            }, $arguments);
        },
        '=' => function ($env, $arguments) {
            return reduce($env, function ($first, $second) {
                return $first == $second;
            }, $arguments);
        },
        '==' => function ($env, $arguments) {
            return reduce($env, function ($first, $second) {
                return $first === $second;
            }, $arguments);
        },
        '!=' => function ($env, $arguments) {
            return reduce($env, function ($first, $second) {
                return $first != $second;
            }, $arguments);
        },
        '!==' => function ($env, $arguments) {
            return reduce($env, function ($first, $second) {
                return $first !== $second;
            }, $arguments);
        }
    ];
}

function reduce($env, $callable, $arguments)
{
    if (empty($arguments)) {
        throw new VMException("Too few arguments");
    }
    $iter = function ($rest, $first) use (&$iter, &$callable, &$env) {
        if (empty($rest)) {
            return true;
        }
        $head = evaluate($env, $rest[0]);
        if ($callable($first, $head)) {
            return $iter(array_slice($rest, 1), $head);
        }
        return false;
    };
    $first = evaluate($env, $arguments[0]);
    $rest = array_slice($arguments, 1);
    return $iter($rest, $first);
}
