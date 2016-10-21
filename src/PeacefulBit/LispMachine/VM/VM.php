<?php

namespace PeacefulBit\LispMachine\VM;

use function PeacefulBit\LispMachine\Calculus\evaluate;
use function PeacefulBit\LispMachine\Environment\makeEnvironment;

use function PeacefulBit\LispMachine\Parser\toAst;
use function PeacefulBit\LispMachine\Parser\toLexemes;

function init(array $modules)
{
    $env = makeEnvironment($modules);
    return function ($code) use ($env) {
        $lexemes = toLexemes($code);
        $tree = toAst($lexemes);
        return evaluate($env, $tree);
    };
}

function initDefaultModules()
{
    return init(array_merge(
        \PeacefulBit\LispMachine\VM\Core\Standard\export(),
        \PeacefulBit\LispMachine\VM\Core\Logical\export(),
        \PeacefulBit\LispMachine\VM\Core\Relative\export(),
        \PeacefulBit\LispMachine\VM\Core\Math\export()
    ));
}
