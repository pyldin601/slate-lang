<?php

namespace tests;

use function Nerd\Common\Arrays\deepMap;

use PeacefulBit\Pocket\Exception\TokenizerException;
use PeacefulBit\Pocket\Parser\Tokenizer;
use PHPUnit\Framework\TestCase;

class TokenizerTest extends TestCase
{
    /**
     * @var Tokenizer
     */
    private $tokenizer;

    public function setUp()
    {
        $this->tokenizer = new Tokenizer();
    }

    public function testEmptyExpression()
    {
        $result = $this->tokenizer->tokenize("");
        $this->assertEmpty($result);
    }

    public function testSymbol()
    {
        $tokens = $this->tokenizer->tokenize("some_symbol");
        $this->assertCount(1, $tokens);

        $token = $tokens[0];

        $this->assertEquals("SymbolToken(some_symbol)", (string) $token);
    }

    public function testDelimiters()
    {
        $this->assertCount(2, $this->tokenizer->tokenize("foo bar"));
        $this->assertCount(2, $this->tokenizer->tokenize("foo \t bar"));
        $this->assertCount(2, $this->tokenizer->tokenize("foo \r\n bar"));
    }

    public function testUnescapedString()
    {
        $tokens = $this->tokenizer->tokenize('"hello world"');
        $this->assertCount(1, $tokens);

        $token = $tokens[0];

        $this->assertEquals("StringToken(hello world)", $token);
    }

    public function testEscapedString()
    {
        $tokens = $this->tokenizer->tokenize('"hello \"world\""');
        $this->assertCount(1, $tokens);

        $token = $tokens[0];

        $this->assertEquals("StringToken(hello \"world\")", $token);
    }

    public function testBrackets()
    {
        $tokens = $this->tokenizer->tokenize('()');
        $this->assertCount(2, $tokens);

        $this->assertEquals('OpenBracketToken CloseBracketToken', implode(' ', $tokens));
    }

    public function testUnclosedString()
    {
        try {
            $this->tokenizer->tokenize('"hello');
            $this->fail("Exception must be thrown");
        } catch (TokenizerException $exception) {
            $this->assertEquals("Unexpected end of string", $exception->getMessage());
        }
    }

    public function testUnusedEscape()
    {
        try {
            $this->tokenizer->tokenize('"hello\\');
            $this->fail("Exception must be thrown");
        } catch (TokenizerException $exception) {
            $this->assertEquals("Unused escape character", $exception->getMessage());
        }
    }

    public function testParseSimpleProgram()
    {
        $code = '(+ 5.6 2.7 (- 15 (/ 4 2)))';
        $tokens = $this->tokenizer->tokenize($code);

        $this->assertCount(14, $tokens);

        $expectedTokens = [
            'OpenBracketToken',
            'SymbolToken(+)',
            'SymbolToken(5.6)',
            'SymbolToken(2.7)',
            'OpenBracketToken',
            'SymbolToken(-)',
            'SymbolToken(15)',
            'OpenBracketToken',
            'SymbolToken(/)',
            'SymbolToken(4)',
            'SymbolToken(2)',
            'CloseBracketToken',
            'CloseBracketToken',
            'CloseBracketToken'
        ];

        $expectedString = implode(' ', $expectedTokens);

        $this->assertEquals($expectedString, implode(' ', $tokens));
    }

    public function testDeflateProgram()
    {
        $code = '(+ 5.6 2.7 (- 15 (/ 4 2)))';
        $tokens = $this->tokenizer->tokenize($code);
        $tree = $this->tokenizer->deflate($tokens);

        $actualTree = deepMap($tree, 'strval');

        $expectedTree = [
            [
                'SymbolToken(+)',
                'SymbolToken(5.6)',
                'SymbolToken(2.7)',
                [
                    'SymbolToken(-)',
                    'SymbolToken(15)',
                    [
                        'SymbolToken(/)',
                        'SymbolToken(4)',
                        'SymbolToken(2)',
                    ]
                ]
            ]
        ];

        $this->assertCount(1, $tree);
        $this->assertEquals($expectedTree, $actualTree);
    }

    public function testComments()
    {
        $code = '(+ 1 2) ; This must be ignored';
        $lexemes = $this->tokenizer->tokenize($code);

        $this->assertCount(6, $lexemes);
    }

    public function testSourceCode()
    {
        $file = __DIR__ . '/fixtures/program.pt';
        $code = file_get_contents($file);
        $tokens = $this->tokenizer->tokenize($code);
        $this->assertTrue(is_array($tokens));
    }
}
