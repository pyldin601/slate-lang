<?php

namespace PeacefulBit\LispMachine\Calculus;

use function Nerd\Common\Arrays\toHeadTail;
use function PeacefulBit\LispMachine\Environment\get;
use function PeacefulBit\LispMachine\Environment\set;

use PeacefulBit\LispMachine\Tree;
use PeacefulBit\LispMachine\VM\VMException;

const NORMAL_ORDER_FUNCTIONS = ['if', 'unless', 'or', 'and'];

/**
 * @param $env
 * @param $node
 * @return mixed
 * @throws VMException
 */
function evaluate($env, $node)
{
    $type = Tree\typeOf($node);
    switch ($type) {
        case Tree\TYPE_SYMBOL:
            return evaluateSymbol($env, $node);
        case Tree\TYPE_STRING:
            return evaluateString($env, $node);
        case Tree\TYPE_EXPRESSION:
            return evaluateExpression($env, $node);
        default:
            throw new VMException("Unknown type of node - $type");
    }
}

function evaluateString($env, $node)
{
    return Tree\valueOf($node);
}

function evaluateSymbol($env, $node)
{
    $value = Tree\valueOf($node);

    if (is_numeric($value)) {
        return is_float($value) ? floatval($value) : intval($value);
    }

    return evaluate($env, get($env, $value));
}

function evaluateExpression($env, $node)
{
    $value = Tree\valueOf($node);
    if (empty($value)) {
        throw new VMException("Empty expression");
    }
    list($function, $arguments) = toHeadTail($value);
    if (Tree\typeOf($function) == Tree\TYPE_SYMBOL && isNormal(Tree\valueOf($function))) {
        return normal($env, $function, $arguments);
    }
    $evaluatedFunction = evaluate($env, $function);
    $evaluatedArguments = array_map(function ($argument) use ($env) {
        return evaluate($env, $argument);
    }, $arguments);
    return apply($env, $evaluatedFunction, $evaluatedArguments);
}

/**
 * @param $function
 * @return bool
 */
function isNormal($function)
{
    return in_array($function, NORMAL_ORDER_FUNCTIONS);
}

/**
 * Normal order reduction.
 *
 * @param $env
 * @param $function
 * @param array $arguments
 * @return null
 */
function normal($env, $function, array $arguments)
{
    return null;
}

/**
 * Applicative order reduction.
 *
 * @param $env
 * @param $function
 * @param array $arguments
 * @return null
 */
function apply($env, $function, array $arguments)
{
    return null;
}


/**
 * Returns new expression where all entries of one terms
 * replaced by other terms depending on terms match table.
 *
 * @param mixed $env
 * @param mixed $expr
 * @param array $match
 */
function substitute($env, $expr, $match)
{
    //
}

/**
 * Reduces expression in context of environment.
 *
 * @param mixed $env
 * @param mixed $expr
 */
function reduce($env, $expr)
{
    //
}

/**
 * Defines abstraction with $name and $args in context
 * of given environment.
 *
 * @param mixed $env
 * @param string $name
 * @param array $args
 * @param mixed $expr
 */
function defineFunction($env, $name, $args, $expr)
{
    //
}

/**
 * Defines constant with $name in context of given environment.
 *
 * @param mixed $env
 * @param string $name
 * @param mixed $expr
 */
function defineConst($env, $name, $expr)
{
    //
}
