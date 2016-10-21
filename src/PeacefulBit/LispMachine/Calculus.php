<?php

namespace PeacefulBit\LispMachine\Calculus;

use function Nerd\Common\Arrays\toHeadTail;
use function PeacefulBit\LispMachine\Environment\get;
use function PeacefulBit\LispMachine\Environment\has;
use function PeacefulBit\LispMachine\Environment\makeEnvironment;
use function PeacefulBit\LispMachine\Environment\set;

use PeacefulBit\LispMachine\Tree;
use PeacefulBit\LispMachine\VM\VMException;

const KEYWORD_DEF = 'def';

/**
 * @param $env
 * @param $node
 * @return mixed
 * @throws VMException
 */
function evaluate($env, $node)
{
    if (is_null($node)) {
        return null;
    }
    $type = Tree\typeOf($node);
    switch ($type) {
        case Tree\TYPE_SYMBOL:
            return evaluateSymbol($env, $node);
        case Tree\TYPE_SEQUENCE:
            return evaluateSequence($env, $node);
        case Tree\TYPE_STRING:
            return evaluateString($env, $node);
        case Tree\TYPE_EXPRESSION:
            return evaluateExpression($env, $node);
        default:
            throw new VMException("Unknown type of node - $type");
    }
}

/**
 * @param $env
 * @param $node
 * @return mixed
 */
function evaluateSequence($env, $node)
{
    return array_reduce(Tree\valueOf($node), function ($_, $current) use ($env) {
        return evaluate($env, $current);
    });
}

/**
 * @param $env
 * @param $node
 * @return mixed
 */
function evaluateString($env, $node)
{
    return Tree\valueOf($node);
}

/**
 * @param $env
 * @param $node
 * @return mixed
 */
function evaluateSymbol($env, $node)
{
    $value = Tree\valueOf($node);

    if (is_numeric($value)) {
        return (substr_count($value, '.') == 1) ? floatval($value) : intval($value);
    }

    return evaluate($env, get($env, $value));
}

/**
 * @param $env
 * @param $node
 * @return null
 * @throws VMException
 */
function evaluateExpression($env, $node)
{
    $value = Tree\valueOf($node);
    if (empty($value)) {
        throw new VMException("Empty expression");
    }
    list($function, $arguments) = toHeadTail($value);
    return apply($env, $function, $arguments);
}

/**
 * @param $env
 * @param $function
 * @param array $arguments
 * @return null
 * @throws VMException
 */
function apply($env, $function, array $arguments)
{
    switch (Tree\typeOf($function)) {
        case Tree\TYPE_SYMBOL:
            switch (Tree\valueOf($function)) {
                case KEYWORD_DEF:
                    if (sizeof($arguments) < 2) {
                        throw new VMException("Too few arguments passed to declaration block");
                    }
                    list($first, $rest) = toHeadTail($arguments);
                    switch (Tree\typeOf($first)) {
                        case Tree\TYPE_EXPRESSION:
                            list($name, $arguments) = toHeadTail(Tree\valueOf($first));
                            if (Tree\typeOf($name) != Tree\TYPE_SYMBOL) {
                                throw new VMException("Function name must be a symbol");
                            }
                            array_walk($arguments, function ($argument) {
                                if (Tree\typeOf($argument) != Tree\TYPE_SYMBOL) {
                                    throw new VMException("Argument name must be a symbol");
                                }
                            });
                            $names = array_map(function ($arg) {
                                return Tree\valueOf($arg);
                            }, $arguments);
                            $body = Tree\node(Tree\TYPE_SEQUENCE, $rest);
                            defineFunction($env, Tree\valueOf($name), $names, $body);
                            break;
                        case Tree\TYPE_SYMBOL:
                            $name = Tree\valueOf($first);
                            $body = Tree\node(Tree\TYPE_SEQUENCE, $rest);
                            defineConst($env, $name, $body);
                            break;
                        default:
                            throw new VMException("Incorrect syntax in declaration block");
                    }
                    return null;
                default:
                    $name = Tree\valueOf($function);
                    if (has($env, $name)) {
                        return call_user_func(get($env, $name), $env, $arguments);
                    }
            }
            break;
        case Tree\TYPE_EXPRESSION:
            $evaluatedFunction = evaluate($env, $function);
            if (has($env, $evaluatedFunction)) {
                return call_user_func(get($env, $evaluatedFunction), $env, $arguments);
            }
            throw new VMException("Symbol $evaluatedFunction does not exist");
    }
    throw new VMException("Unsupported type of expression passed as function name");
}

function callFunction($env, $name, $arguments)
{
    if (!has($env, $name)) {
        throw new VMException("Symbol $name does not exist");
    }
    $expression = get($env, $name);
    if (is_callable($expression)) {
        return call_user_func($expression, $env, $arguments);
    }
    throw new VMException("Symbol $name is not callable");
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
    $function = function ($env, $argValues) use ($args, $expr) {
        $functionContext = array_combine($args, $argValues);
        $newEnv = makeEnvironment($functionContext, $env);
        return evaluate($newEnv, $expr);
    };
    set($env, $name, $function);
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
    set($env, $name, $expr);
}
