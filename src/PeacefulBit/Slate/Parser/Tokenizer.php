<?php

namespace PeacefulBit\Slate\Parser;

use function Nerd\Common\Arrays\append;
use function Nerd\Common\Arrays\toHeadTail;
use function Nerd\Common\Functional\tail;
use function Nerd\Common\Strings\toArray;

use PeacefulBit\Slate\Exceptions\TokenizerException;
use PeacefulBit\Slate\Parser\Tokens;

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

    const CHAR_DIGITS           = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
    const CHAR_DOT              = '.';

    /**
     * @param $char
     * @return bool
     */
    private function isStructural($char)
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
    private function isDelimiter($char)
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
    private function isSymbol($char)
    {
        return !$this->isDelimiter($char) && !$this->isStructural($char);
    }

    /**
     * @param $char
     * @return bool
     */
    private function isNumber($char)
    {
        return in_array($char, self::CHAR_DIGITS);
    }

    /**
     * Convert source code to array of tokens.
     *
     * @param string $code
     * @return Token[]
     */
    public function tokenize($code)
    {
        // Initial state of parser
        $baseIter = tail(function ($rest, $acc) use (&$baseIter, &$symbolIter, &$stringIter, &$commentIter) {
            if (sizeof($rest) == 0) {
                return $acc;
            }
            list ($head, $tail) = toHeadTail($rest);
            switch ($head) {
                // We got '(', so we just add it to accumulator.
                case self::TOKEN_OPEN_BRACKET:
                    return $baseIter($tail, append($acc, new Tokens\OpenBracketToken));
                // We got ')', and doing the same as in previous case.
                case self::TOKEN_CLOSE_BRACKET:
                    return $baseIter($tail, append($acc, new Tokens\CloseBracketToken));
                // We got '"'. That means that we're in the beginning of the string.
                // So we switch our state to stringIter.
                case self::TOKEN_DOUBLE_QUOTE:
                    return $stringIter($tail, '', $acc);
                // We got ';'. And that means that comment is starting here. So we
                // change our state to commentIter.
                case self::TOKEN_SEMICOLON:
                    return $commentIter($tail, '', $acc);
                default:
                    // If current char is a delimiter, we ignore it.
                    if ($this->isDelimiter($head)) {
                        return $baseIter($tail, $acc);
                    }
                    // In all other cases we interpret current char as first char
                    // of symbol and change our state to symbolIter.
                    return $symbolIter($tail, $head, $acc);
            }
        });

        // State when parser parses any symbol
        $symbolIter = tail(function ($rest, $buffer, $acc) use (&$symbolIter, &$baseIter, &$preDotSymbolIter) {
            if (sizeof($rest) > 0) {
                list ($head, $tail) = toHeadTail($rest);
                if ($head == self::CHAR_DOT) {
                    return $preDotSymbolIter($tail, $buffer . $head, $acc);
                }
                if ($this->isSymbol($head)) {
                    return $symbolIter($tail, $buffer . $head, $acc);
                }
            }
            if (is_numeric($buffer)) {
                $symbolToken = new Tokens\NumericToken($buffer);
            } else {
                $symbolToken = new Tokens\IdentifierToken($buffer);
            }
            return $baseIter($rest, append($acc, $symbolToken));
        });

        $preDotSymbolIter = tail(function ($rest, $buffer, $acc) use (&$baseIter, &$dotSymbolIter) {
            if (sizeof($rest) > 0) {
                list ($head, $tail) = toHeadTail($rest);
                if ($this->isSymbol($head)) {
                    return $dotSymbolIter($tail, $buffer . $head, $acc);
                }
            }
            throw new TokenizerException("Unexpected dot at the end of identifier");
        });

        // State when parser parses any symbol
        $dotSymbolIter = tail(function ($rest, $buffer, $acc) use (&$baseIter, &$dotSymbolIter) {
            if (sizeof($rest) > 0) {
                list ($head, $tail) = toHeadTail($rest);
                if ($head == '.') {
                    throw new TokenizerException("Only one dot allowed");
                }
                if ($this->isSymbol($head)) {
                    return $dotSymbolIter($tail, $buffer . $head, $acc);
                }
            }
            if (is_numeric($buffer)) {
                $symbolToken = new Tokens\NumericToken($buffer);
            } else {
                $symbolToken = new Tokens\DotIdentifierToken($buffer);
            }
            return $baseIter($rest, append($acc, $symbolToken));
        });

        // State when parser parses string
        $stringIter = tail(function ($rest, $buffer, $acc) use (&$stringIter, &$baseIter, &$escapeIter) {
            if (sizeof($rest) == 0) {
                throw new TokenizerException("Unexpected end of string");
            }
            list ($head, $tail) = toHeadTail($rest);
            if ($head == self::TOKEN_DOUBLE_QUOTE) {
                return $baseIter($tail, append($acc, new Tokens\StringToken($buffer)));
            }
            if ($head == Tokenizer::TOKEN_BACK_SLASH) {
                return $escapeIter($tail, $buffer, $acc);
            }
            return $stringIter($tail, $buffer . $head, $acc);
        });

        // State when parser parses escaped symbol
        $escapeIter = tail(function ($rest, $buffer, $acc) use (&$stringIter) {
            if (sizeof($rest) == 0) {
                throw new TokenizerException("Unused escape character");
            }
            list ($head, $tail) = toHeadTail($rest);
            return $stringIter($tail, $buffer . $head, $acc);
        });

        // State when parser ignores comments
        $commentIter = function ($rest, $buffer, $acc) use (&$commentIter, &$baseIter) {
            if (sizeof($rest) > 0) {
                list ($head, $tail) = toHeadTail($rest);
                if ($head != Tokenizer::TOKEN_NEW_LINE) {
                    return $commentIter($tail, $buffer . $head, $acc);
                }
            }
            return $baseIter($rest, $acc);
        };

        return $baseIter(toArray($code), []);
    }
}
