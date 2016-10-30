<?php

namespace PeacefulBit\Slate\Parser;

use function Nerd\Common\Arrays\{
    all, toHeadTail, append
};
use function Nerd\Common\Functional\tail;

use PeacefulBit\Slate\Exceptions\ParserException;
use PeacefulBit\Slate\Parser\Nodes;
use PeacefulBit\Slate\Parser\Tokens;

class Parser
{
    /**
     * Convert tokens tree to abstract syntax tree.
     *
     * @param Tokens\Token[] $tokens
     * @return Nodes\Node
     */
    public function parse(array $tokens): Nodes\Node
    {
        $tokensTree = $this->deflate($tokens);

        return $this->parseProgram($tokensTree);
    }

    /**
     * @param $token
     * @return Nodes\Identifier|Nodes\Literal|Nodes\Node
     * @throws ParserException
     */
    public function parseToken($token)
    {
        if (is_array($token)) {
            return $this->parseExpression($token);
        }
        if ($token instanceof Tokens\StringToken) {
            return $this->parseString($token);
        }
        if ($token instanceof Tokens\IdentifierToken) {
            return $this->parseIdentifier($token);
        }
        if ($token instanceof Tokens\NumericToken) {
            return $this->parseNumeric($token);
        }
        throw new ParserException("Unexpected type of token - $token.");
    }

    /**
     * @param array $tokens
     * @return Nodes\Node
     * @throws ParserException
     */
    private function parseExpression(array $tokens): Nodes\Node
    {
        if (empty($tokens)) {
            throw new ParserException("Expression could not be empty.");
        }

        list ($head, $tail) = toHeadTail($tokens);

        if ($head instanceof Tokens\IdentifierToken) {
            $id = $head->getValue();

            if ($id == 'def') {
                return $this->parseDeclaration($tail);
            }

            if ($id == 'def-macro') {
                return $this->parseMacroDeclaration($tail);
            }

            if ($id == 'lambda') {
                return $this->parseLambdaDeclaration($tail);
            }
        }

        return $this->parseCallExpression($tokens);
    }

    /**
     * @param array $tokens
     * @return Nodes\Node
     */
    private function parseDeclaration($tokens): Nodes\Node
    {
        list ($head, $tail) = $tokens;

        if (!is_array($head)) {
            return $this->parseAssignDeclaration($tokens);
        }

        return $this->parseFunctionDeclaration($tokens);
    }

    /**
     * @param $tokens
     * @return Nodes\Node
     * @throws ParserException
     */
    private function parseLambdaDeclaration($tokens): Nodes\Node
    {
        list ($head, $body) = toHeadTail($tokens);

        $test = all($head, function ($token) {
            return $token instanceof Tokens\IdentifierToken;
        });

        if (!$test) {
            throw new ParserException("Lambda arguments must be valid identifiers.");
        }

        return new Nodes\LambdaExpression($head, $this->parseSequence($body));
    }

    /**
     * @param $tokens
     * @return Nodes\Node
     * @throws ParserException
     */
    private function parseFunctionDeclaration($tokens): Nodes\Node
    {
        list ($head, $body) = toHeadTail($tokens);

        if (sizeof($head) < 1) {
            throw new ParserException("Function must have identifier.");
        }

        $headValues = array_map(function ($token) {
            if (!$token instanceof Tokens\IdentifierToken) {
                throw new ParserException("Function name and arguments must be valid identifiers.");
            }
            return $token->getValue();
        }, $head);

        list ($name, $args) = toHeadTail($headValues);

        return new Nodes\FunctionExpression($name, $args, $this->parseSequence($body));
    }

    private function parseMacroDeclaration($tokens): Nodes\Node
    {
        list ($head, $body) = toHeadTail($tokens);

        if (sizeof($head) < 1) {
            throw new ParserException("Macro must have identifier.");
        }

        $headValues = array_map(function ($token) {
            if (!$token instanceof Tokens\IdentifierToken) {
                throw new ParserException("Macro name and arguments must be valid identifiers.");
            }
            return $token->getValue();
        }, $head);

        list ($name, $args) = toHeadTail($headValues);

        return new Nodes\MacroExpression($name, $args, $this->parseSequence($body));
    }

    /**
     * @param array $tokens
     * @return Nodes\Assign
     * @throws ParserException
     */
    private function parseAssignDeclaration(array $tokens): Nodes\Assign
    {
        if (sizeof($tokens) % 2 != 0) {
            throw new ParserException("Bad assign declaration");
        }

        $assigns = array_chunk($tokens, 2);

        $variables = array_reduce($assigns, function ($result, $assign) {
            if (!$assign[0] instanceof Tokens\IdentifierToken) {
                throw new ParserException("Bad type of identifier in assign declaration.");
            }
            return array_merge($result, [$assign[0]->getValue() => $this->parseToken($assign[1])]);
        }, []);

        return new Nodes\Assign(array_keys($variables), array_values($variables));
    }

    /**
     * @param array $tokens
     * @return Nodes\Node
     */
    private function parseCallExpression(array $tokens): Nodes\Node
    {
        list ($head, $tail) = toHeadTail($tokens);

        return new Nodes\CallExpression(
            $this->parseToken($head),
            array_map([$this, 'parseToken'], $tail)
        );
    }

    /**
     * @param array $tokens
     * @return Nodes\Program
     */
    private function parseProgram(array $tokens): Nodes\Program
    {
        return new Nodes\Program(array_map([$this, 'parseToken'], $tokens));
    }

    /**
     * @param array $tokens
     * @return Nodes\SequenceExpression
     */
    private function parseSequence(array $tokens): Nodes\SequenceExpression
    {
        return new Nodes\SequenceExpression(array_map([$this, 'parseToken'], $tokens));
    }

    /**
     * @param Tokens\IdentifierToken $token
     * @return Nodes\Identifier
     */
    private function parseIdentifier(Tokens\IdentifierToken $token): Nodes\Identifier
    {
        return new Nodes\Identifier($token->getValue());
    }

    /**
     * @param Tokens\StringToken $token
     * @return Nodes\Literal
     */
    public function parseString(Tokens\StringToken $token): Nodes\Literal
    {
        return new Nodes\Literal($token->getValue());
    }

    /**
     * @param Tokens\NumericToken $token
     * @return Nodes\Literal
     */
    public function parseNumeric(Tokens\NumericToken $token): Nodes\Literal
    {
        return new Nodes\Literal($token->getValue());
    }

    /**
     * Convert list of tokens into token tree.
     *
     * @param Tokens\Token[] $tokens
     * @return mixed
     */
    private function deflate(array $tokens)
    {
        $iter = tail(function ($rest, $acc) use (&$iter) {
            if (empty($rest)) {
                return $acc;
            }
            list ($head, $tail) = toHeadTail($rest);
            switch (get_class($head)) {
                case Tokens\OpenBracketToken::class:
                    $pairClosingIndex = $this->findPairClosingBracketIndex($tail);
                    $inner = array_slice($tail, 0, $pairClosingIndex);
                    $innerNode = $this->deflate($inner);
                    $newTail = array_slice($tail, $pairClosingIndex + 1);
                    return $iter($newTail, append($acc, $innerNode));
                case Tokens\CloseBracketToken::class:
                    throw new ParserException("Unpaired opening bracket found");
                default:
                    return $iter($tail, append($acc, $head));
            }
        });
        return $iter($tokens, []);
    }

    /**
     * @param array $tokens
     * @return mixed
     */
    private function findPairClosingBracketIndex(array $tokens)
    {
        $iter = tail(function ($rest, $depth, $position) use (&$iter) {
            if (empty($rest)) {
                throw new ParserException("Unpaired closing bracket found.");
            }
            list ($head, $tail) = toHeadTail($rest);
            if ($head instanceof Tokens\CloseBracketToken) {
                if ($depth == 0) {
                    return $position;
                }
                return $iter($tail, $depth - 1, $position + 1);
            }
            if ($head instanceof Tokens\OpenBracketToken) {
                return $iter($tail, $depth + 1, $position + 1);
            }
            return $iter($tail, $depth, $position + 1);
        });
        return $iter($tokens, 0, 0);
    }
}
