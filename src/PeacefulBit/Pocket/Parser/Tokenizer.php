<?php

namespace PeacefulBit\Pocket\Parser;

use function Nerd\Common\Arrays\append;
use function Nerd\Common\Arrays\toHeadTail;
use function Nerd\Common\Functional\tail;
use function Nerd\Common\Strings\toArray;

use PeacefulBit\Pocket\Exception\ParserException;
use PeacefulBit\Pocket\Exception\SyntaxException;
use PeacefulBit\Pocket\Exception\TokenizerException;
use PeacefulBit\Pocket\Parser\Nodes\ConstantNode;
use PeacefulBit\Pocket\Parser\Nodes\SequenceNode;
use PeacefulBit\Pocket\Parser\Nodes\StringNode;
use PeacefulBit\Pocket\Parser\Nodes\SymbolNode;
use PeacefulBit\Pocket\Parser\Tokens\CloseBracketToken;
use PeacefulBit\Pocket\Parser\Tokens\CommentToken;
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
     * Convert source code to list of tokens.
     *
     * @param string $code
     * @return array
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
                    return $baseIter($tail, append($acc, new OpenBracketToken));
                // We got ')', and doing the same as in previous case.
                case self::TOKEN_CLOSE_BRACKET:
                    return $baseIter($tail, append($acc, new CloseBracketToken));
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
        $symbolIter = tail(function ($rest, $buffer, $acc) use (&$symbolIter, &$baseIter, &$delimiterIter) {
            if (sizeof($rest) > 0) {
                list ($head, $tail) = toHeadTail($rest);
                if ($this->isSymbol($head)) {
                    return $symbolIter($tail, $buffer . $head, $acc);
                }
            }
            $symbolToken = new SymbolToken($buffer);
            return $baseIter($rest, append($acc, $symbolToken));
        });

        // State when parser parses string
        $stringIter = tail(function ($rest, $buffer, $acc) use (&$stringIter, &$baseIter, &$escapeIter) {
            if (sizeof($rest) == 0) {
                throw new TokenizerException("Unexpected end of string");
            }
            list ($head, $tail) = toHeadTail($rest);
            if ($head == self::TOKEN_DOUBLE_QUOTE) {
                return $baseIter($tail, append($acc, new StringToken($buffer)));
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
            return $baseIter($rest, append($acc, new CommentToken(trim($buffer))));
        };

        return $baseIter(toArray($code), []);
    }

    /**
     * Convert list of tokens to abstract tree.
     *
     * @param array $tokens
     * @return mixed
     */
    public function deflate(array $tokens)
    {
        $iter = tail(function ($rest, $acc) use (&$iter) {
            if (empty($rest)) {
                return $acc;
            }
            list ($head, $tail) = toHeadTail($rest);
            switch (get_class($head)) {
                case OpenBracketToken::class:
                    $pairClosingIndex = $this->findPairClosingBracketIndex($tail);
                    $inner = array_slice($tail, 0, $pairClosingIndex);
                    $innerNode = $this->deflate($inner);
                    $newTail = array_slice($tail, $pairClosingIndex + 1);
                    return $iter($newTail, append($acc, $innerNode));
                case CloseBracketToken::class:
                    throw new SyntaxException("Unpaired opening bracket found");
                default:
                    return $iter($tail, append($acc, $head));
            }
        });
        return $iter($tokens, []);
    }

    public function deflatedToNodes(array $tree)
    {
        return new SequenceNode(array_reduce($tree, function ($acc, $token) {
            return $this->isValueToken($token)
                ? append($acc, $this->convertToNode($token))
                : $acc;
        }, []));
    }

    private function isValueToken($token)
    {
        return is_array($token) || $token instanceof SymbolToken || $token instanceof StringToken;
    }

    private function convertToNode($token)
    {
        if (is_array($token)) {
            return $this->convertExpressionToNode($token);
        }
        if ($token instanceof SymbolToken) {
            return new SymbolNode($token->getContent());
        }
        if ($token instanceof StringToken) {
            return new StringNode($token->getContent());
        }
        throw new TokenizerException("Unexpected type of token");
    }

    private function convertExpressionToNode($expression)
    {
        if (empty($expression)) {
            throw new ParserException("Expression must have a body");
        }

        list ($head, $tail) = toHeadTail($expression);

        if ($head instanceof SymbolToken && $head == 'def') {
            return $this->convertDefineToNode($tail);
        }
    }

    private function convertDefineToNode($body)
    {
        if (empty($expression)) {
            throw new ParserException("Define expression must have a body");
        }

        $head = $expression[0];

        if (is_array($head)) {
            return $this->convertFunctionToNode($body);
        }
        return $this->convertConstantToNode($body);
    }

    private function convertFunctionToNode($body)
    {
        list ($head, $tail) = toHeadTail($body);
        if (sizeof($head) < 1) {
            throw new ParserException("Function must have a name");
        }
        array_walk($head, function ($token) {
            if (!$token instanceof SymbolToken) {
                throw new ParserException("Function name and arguments must be symbols");
            }
        });
        list ($name, $args) = toHeadTail($head);

    }

    private function convertConstantToNode($body)
    {
        if (sizeof($body) % 2 != 0) {
            throw new ParserException("Wrong number of arguments passed to constant define");
        }

        $chunks = array_chunk($body, 2);
        $constants = array_reduce($chunks, function ($acc, $chunk) {
            list ($name, $value) = $chunk;
            if (!$name instanceof SymbolToken) {
                throw new ParserException("Constant name must be a symbol");
            }
            return array_merge($acc, [$name->getContent() => $this->convertToNode($value)]);
        }, []);

        return new ConstantNode(array_keys($constants), array_values($constants));
    }

    private function findPairClosingBracketIndex(array $tokens)
    {
        $iter = tail(function ($rest, $depth, $position) use (&$iter) {
            if (empty($rest)) {
                throw new SyntaxException("Unpaired closing bracket found");
            }
            list ($head, $tail) = toHeadTail($rest);
            if ($head instanceof CloseBracketToken) {
                if ($depth == 0) {
                    return $position;
                }
                return $iter($tail, $depth - 1, $position + 1);
            }
            if ($head instanceof OpenBracketToken) {
                return $iter($tail, $depth + 1, $position + 1);
            }
            return $iter($tail, $depth, $position + 1);
        });
        return $iter($tokens, 0, 0);
    }
}
