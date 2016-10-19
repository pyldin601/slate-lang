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

function toLexemes($code)
{
    $iter = function ($rest, $buffer, $state, $acc) use (&$iter) {
        switch ($state) {
            case STATE_INIT:
            case STATE_SYMBOL:
            case STATE_STRING:
            case STATE_ESCAPE:
        }
    };
    return $iter($code, [], STATE_INIT, []);
}