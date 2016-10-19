<?php

namespace PeacefulBit\Lisp\Parser;

const LEXEME_DELIMITER      = 0;
const LEXEME_OPEN_BRACKET   = 1;
const LEXEME_CLOSE_BRACKET  = 2;
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
function isStructural($char)
{
    return in_array($char, ['(', ')', '"']);
}

/**
 * @param $char
 * @return bool
 */
function isSymbol($char)
{
    return !isDelimiter($char) && !isStructural($char);
}

/**
 * @param $char
 * @return bool
 */
function isDelimiter($char)
{
    return in_array($char, ["\t", "\r", "\n", " "]);
}

/**
 * Covert code to list of lexemes using iterative state machine.
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
            case '(':
                return $baseIter($tail, array_merge($acc, [makeLexeme(LEXEME_OPEN_BRACKET)]));
            case ')':
                return $baseIter($tail, array_merge($acc, [makeLexeme(LEXEME_CLOSE_BRACKET)]));
            case '"':
                return $stringIter($tail, [], $acc);
            default:
                if (isDelimiter($head)) {
                    return $baseIter($tail, $acc);
                }
                return $symbolIter($tail, [$head], $acc);
        }
    };

    $symbolIter = function ($rest, $buffer, $acc) use (&$symbolIter, &$baseIter) {
        if (!empty($rest)) {
            $head = $rest[0];
            $tail = substr($rest, 1);
            if (isSymbol($head)) {
                return $symbolIter($tail, array_merge($buffer, [$head]), $acc);
            }
        }
        $lexeme = makeLexeme(LEXEME_SYMBOL, $buffer);
        return $baseIter($rest, array_merge($acc, [$lexeme]));
    };

    $stringIter = function ($rest, $buffer, $acc) use (&$stringIter, &$baseIter, &$escapeIter) {
        if (empty($rest)) {
            $bufferString = implode('', $buffer);
            throw new ParserException("Unexpected end of string after \"$bufferString\"");
        }
        $head = $rest[0];
        $tail = substr($rest, 1);
        if ($head == '"') {
            $lexeme = makeLexeme(LEXEME_STRING, $buffer);
            return $baseIter($tail, array_merge($acc, [$lexeme]));
        }
        if ($head == '\\') {
            return $escapeIter($tail, $buffer, $acc);
        }
        return $stringIter($tail, array_merge($buffer, [$head]), $acc);
    };

    $escapeIter = function ($rest, $buffer, $acc) use (&$stringIter) {
        if (empty($rest)) {
            $bufferString = implode('', $buffer);
            throw new ParserException("Unused escape character after \"$bufferString\"");
        }
        $head = $rest[0];
        $tail = substr($rest, 1);
        return $stringIter($tail, array_merge($buffer, [$head]), $acc);
    };

    return $baseIter($code, []);
}
