<?php

namespace PeacefulBit\LispMachine\VM;

use PeacefulBit\LispMachine\Lexer;

use PeacefulBit\LispMachine\Parser\ParserException;

const TOKEN_OR = 'or';
const TOKEN_AND = 'and';
const TOKEN_NOT = 'not';

/**
 * @param $env
 * @param $ast
 * @return mixed
 */
function evaluate($env, $ast)
{
    return array_reduce($ast, function ($env, $expression) {
        return evaluateLexeme($env, $expression);
    }, $env);
}

/**
 * Evaluate single expression.
 *
 * @param $env
 * @param $expression
 * @return mixed
 * @throws ParserException
 */
function evaluateExpression($env, $expression)
{
    if (Lexer\isLexeme($expression)) {
        return evaluateLexeme($env, $expression);
    }

    if (sizeof($expression) == 0) {
        throw new ParserException("Empty expression");
    }

    $id = $expression[0];
    $type = Lexer\getType($id);

    if ($type != Lexer\LEXEME_SYMBOL) {
        throw new ParserException("Id must be a symbol");
    }

    return $env;
}

/**
 * Evaluate single lexeme.
 *
 * @param $env
 * @param $lexeme
 * @return mixed
 */
function evaluateLexeme($env, $lexeme)
{
    return [$env, null];
}
