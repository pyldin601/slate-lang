<?php

namespace Lisp\VM\Parser;

const LEXEME_DELIMITER      = 0;
const LEXEME_OPEN_PAREN     = 1;
const LEXEME_CLOSE_PAREN    = 2;
const LEXEME_SYMBOL         = 4;
const LEXEME_STRING         = 5;

const STATE_INIT            = 1;
const STATE_SYMBOL          = 2;
const STATE_STRING          = 3;
const STATE_ESCAPE          = 4;

/**
 * Make lexeme item.
 *
 * @param string $type
 * @param mixed $data
 * @return array
 */
function makeLexeme($type, $data = null)
{
    return [$type, $data];
}

/**
 * @param $char
 * @return bool
 */
function isSymbol($char)
{
    return !in_array($char, ['(', ')', '"']);
}

/**
 * Covert code to list of lexemes.
 *
 * @param $code
 * @return mixed
 */
function toLexemes($code)
{
    $baseIter = function ($rest, $acc) use (&$baseIter, &$symbolIter, &$stringIter) {
        if (empty($rest)) {
            return $acc;
        }
        $head = $rest[0];
        $tail = substr($rest, 1);
        switch ($head) {
            case ' ':
            case "\n":
            case "\r":
            case "\t":
                return $baseIter($tail, $acc);
            case '(':
                return $baseIter($tail, array_merge($acc, [makeLexeme(LEXEME_OPEN_PAREN)]));
            case ')':
                return $baseIter($tail, array_merge($acc, [makeLexeme(LEXEME_CLOSE_PAREN)]));
            case '"':
                return $stringIter($tail, [], $acc);
            default:
                return $symbolIter($tail, [$head], $acc);
        }
    };

    $symbolIter = function ($rest, $buffer, $acc) use (&$symbolIter) {
        if (empty($rest)) {
            throw new \Exception();
        }
        $head = $rest[0];
        $tail = substr($rest, 1);
        switch ($head) {

        }
    };

    $stringIter = function ($rest, $buffer, $acc) {

    };

    $iter = function ($rest, $buffer, $state, $acc) use (&$iter) {
        $current = $rest[0];
        switch ($state) {
            case STATE_INIT:
                switch ($current) {
                    case '(':
                        return $iter(substr($rest, 1), [], STATE_INIT, array_merge($acc, [makeLexeme(LEXEME_OPEN_PAREN)]));
                }
            case STATE_SYMBOL:
            case STATE_STRING:
            case STATE_ESCAPE:
        }
    };

    return $baseIter($code[]);
}
