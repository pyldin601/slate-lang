<?php

namespace tests;

use function PeacefulBit\LispMachine\Calculus\evaluate;
use function PeacefulBit\LispMachine\Environment\has;
use function PeacefulBit\LispMachine\Environment\makeEnvironment;
use function PeacefulBit\LispMachine\Parser\toAst;
use function PeacefulBit\LispMachine\Parser\toLexemes;

use PHPUnit\Framework\TestCase;

/**
 * Class CalculusTest
 * @package tests
 * @after tests\TreeTest
 */
class CalculusTest extends TestCase
{
    public function testVariableDeclaration()
    {
        $lexemes = toLexemes("(def f 10)");
        $ast = toAst($lexemes);
        $env = makeEnvironment();
        evaluate($env, $ast);
        $this->assertTrue(has($env, 'f'));
    }

    public function testFunctionDeclaration()
    {
        $lexemes = toLexemes("(def (foo x) (/ x 2))");
        $ast = toAst($lexemes);
        $env = makeEnvironment();
        evaluate($env, $ast);
        $this->assertTrue(has($env, 'foo'));
    }
}
