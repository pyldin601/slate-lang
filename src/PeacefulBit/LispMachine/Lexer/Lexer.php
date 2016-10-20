<?php

namespace PeacefulBit\LispMachine\Lexer;

const LEXEME_DELIMITER      = 100;
const LEXEME_OPEN_BRACKET   = 101;
const LEXEME_CLOSE_BRACKET  = 102;
const LEXEME_SYMBOL         = 104;
const LEXEME_STRING         = 105;

const LEXEME_ID             = '$$lex$$';

/**
 * Make lexeme.
 *
 * @param string $type
 * @param mixed $data
 * @return array
 */
function makeLexeme($type, $data = null)
{
    return [LEXEME_ID, $type, $data];
}

/**
 * Check whether object is valid lexeme.
 *
 * @param $lexeme
 * @return bool
 */
function isLexeme($lexeme)
{
    return is_array($lexeme) && sizeof($lexeme) == 3 && $lexeme[0] == LEXEME_ID;
}

/**
 * Get lexeme type.
 *
 * @param $lexeme
 * @return mixed
 */
function getType($lexeme)
{
    return $lexeme[1];
}

/**
 * Get lexeme data.
 *
 * @param $lexeme
 * @return mixed
 */
function getData($lexeme)
{
    return $lexeme[2];
}
