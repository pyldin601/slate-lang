<?php

namespace PeacefulBit\Pocket\Parser;

use function Nerd\Common\Arrays\append;
use function Nerd\Common\Arrays\toHeadTail;

use PeacefulBit\Pocket\Exception\TokenizerException;
use PeacefulBit\Pocket\Parser\Nodes\SymbolNode;
use PeacefulBit\Pocket\Parser\Tokens\CloseBracketToken;
use PeacefulBit\Pocket\Parser\Tokens\CommentToken;
use PeacefulBit\Pocket\Parser\Tokens\DelimiterToken;
use PeacefulBit\Pocket\Parser\Tokens\OpenBracketToken;
use PeacefulBit\Pocket\Parser\Tokens\StringToken;
use PeacefulBit\Pocket\Parser\Tokens\SymbolToken;

class Tokenizer
{
    const TOKEN_OPEN_BRACKET    = '(';
    const TOKEN_CLOSE_BRACKET   = ')';
    const TOKEN_DOUBLE_QUOTE    = '"';
    const TOKEN_BACK_SLASH      = '\\';
    const TOKEN_SEMICOLON       = ';';

    const TOKEN_TAB             = "\t";
    const TOKEN_SPACE           = " ";
    const TOKEN_NEW_LINE        = "\n";
    const TOKEN_CARRIAGE_RETURN = "\r";

    /**
     * @param $char
     * @return bool
     */
    public static function isStructural($char)
    {
        return in_array($char, [
            self::TOKEN_OPEN_BRACKET,
            self::TOKEN_CLOSE_BRACKET,
            self::TOKEN_DOUBLE_QUOTE,
            self::TOKEN_SEMICOLON
        ]);
    }

    /**
     * @param $char
     * @return bool
     */
    public static function isDelimiter($char)
    {
        return in_array($char, [
            self::TOKEN_TAB,
            self::TOKEN_CARRIAGE_RETURN,
            self::TOKEN_NEW_LINE,
            self::TOKEN_SPACE
        ]);
    }

    /**
     * @param $char
     * @return bool
     */
    public static function isSymbol($char)
    {
        return !self::isDelimiter($char) && !self::isStructural($char);
    }

    /**
     * @param string $code
     * @return array
     */
    public static function tokenize($code)
    {
        // Initial state of parser
        $baseIter = function ($rest, $acc) use (&$baseIter, &$symbolIter, &$stringIter, &$commentIter) {
            if (sizeof($rest) == 0) {
                return $acc;
            }
            list ($head, $tail) = toHeadTail($rest);
            switch ($head) {
                // We got '(', so we just add it to list of lexemes.
                case self::TOKEN_OPEN_BRACKET:
                    return $baseIter($tail, append($acc, new OpenBracketToken));
                // We got ')' and doing the same as in previous case.
                case self::TOKEN_CLOSE_BRACKET:
                    return $baseIter($tail, append($acc, new CloseBracketToken));
                // We got '"'! It means that we are at the beginning of the string
                // and must switch our state to stringIter.
                case self::TOKEN_DOUBLE_QUOTE:
                    return $stringIter($tail, '', $acc);
                // We got ';'. It means that comment is starting here. So we
                // change our state to commentIter.
                case self::TOKEN_SEMICOLON:
                    return $commentIter($tail, '', $acc);
                default:
                    // If current char is a delimiter, we just ignore it.
                    if (self::isDelimiter($head)) {
                        return $baseIter($tail, $acc);
                    }
                    // In all other cases we interpret current char as start
                    // of symbol and change our state to symbolIter
                    return $symbolIter($tail, $head, $acc);
            }
        };

        // State when parser parses any symbol
        $symbolIter = function ($rest, $buffer, $acc) use (&$symbolIter, &$baseIter, &$delimiterIter) {
            if (sizeof($rest) > 0) {
                list ($head, $tail) = toHeadTail($rest);
                if (self::isSymbol($head)) {
                    return $symbolIter($tail, $buffer . $head, $acc);
                }
            }
            $symbolToken = new SymbolToken($buffer);
            return $baseIter($rest, append($acc, $symbolToken));
        };

        // State when parser parses string
        $stringIter = function ($rest, $buffer, $acc) use (&$stringIter, &$baseIter, &$escapeIter) {
            if (sizeof($rest) == 0) {
                throw new TokenizerException("Unexpected end of string");
            }
            list ($head, $tail) = toHeadTail($rest);
            if ($head == self::TOKEN_DOUBLE_QUOTE) {
                return $baseIter($tail, append($acc, new StringToken($buffer)));
            }
            if ($head == '\\') {
                return $escapeIter($tail, $buffer, $acc);
            }
            return $stringIter($tail, $buffer . $head, $acc);
        };

        // State when parser parses escaped symbol
        $escapeIter = function ($rest, $buffer, $acc) use (&$stringIter) {
            if (sizeof($rest) == 0) {
                throw new TokenizerException("Unused escape character");
            }
            list ($head, $tail) = toHeadTail($rest);
            return $stringIter($tail, $buffer . $head, $acc);
        };

        // State when parser ignores comments
        $commentIter = function ($rest, $buffer, $acc) use (&$commentIter, &$baseIter) {
            if (sizeof($rest) > 0) {
                list ($head, $tail) = toHeadTail($rest);
                if ($head != Tokenizer::TOKEN_NEW_LINE) {
                    return $commentIter($tail, $buffer . $head, $acc);
                }
            }
            return $baseIter($rest, append($acc, new CommentToken($buffer)));
        };

        // todo: to be or not to be
        $delimiterIter = function ($rest, $buffer, $acc) use (&$delimiterIter, &$baseIter) {
            if (sizeof($rest) > 0) {
                list ($head, $tail) = toHeadTail($rest);
                if (self::isDelimiter($head)) {
                    return $delimiterIter($tail, $buffer . $head, $acc);
                }
            }
            return $baseIter($rest, append($acc, new DelimiterToken($buffer)));
        };

        return $baseIter(str_split($code), []);
    }
}
