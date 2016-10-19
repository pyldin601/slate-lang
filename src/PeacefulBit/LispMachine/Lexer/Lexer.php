<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 10/19/16
 * Time: 1:46 PM
 */

namespace PeacefulBit\LispMachine\Lexer;

const LEXEME_DELIMITER      = 100;
const LEXEME_OPEN_BRACKET   = 101;
const LEXEME_CLOSE_BRACKET  = 102;
const LEXEME_SYMBOL         = 104;
const LEXEME_STRING         = 105;

/**
 * Make lexeme.
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
 * Check whether object is valid lexeme.
 *
 * @param $lexeme
 * @return bool
 */
function isLexeme($lexeme)
{
    return is_array($lexeme) && sizeof($lexeme) == 2;
}

/**
 * Get lexeme type.
 *
 * @param $lexeme
 * @return mixed
 */
function getType($lexeme)
{
    return $lexeme[0];
}

/**
 * Get lexeme data.
 *
 * @param $lexeme
 * @return mixed
 */
function getData($lexeme)
{
    return $lexeme[1];
}

/**
 * @param $lexemes
 * @return array
 */
function getListOfTypes($lexemes)
{
    return array_map('\PeacefulBit\LispMachine\Lexer\getType', $lexemes);
}
