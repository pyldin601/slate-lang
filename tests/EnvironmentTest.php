<?php

namespace tests;

use PHPUnit\Framework\TestCase;

use function PeacefulBit\LispMachine\Environment\makeEnvironment;
use function PeacefulBit\LispMachine\Environment\has;
use function PeacefulBit\LispMachine\Environment\get;

class EnvironmentTest extends TestCase
{
    public function testCreateEnvironment()
    {
        $env = makeEnvironment();
        $this->assertFalse(has($env, 'foo'));
        $this->assertNull(get($env, 'foo'));
    }

    public function testAccessors()
    {
        $env = makeEnvironment([
            'foo' => function () {
                return 'bar';
            }
        ]);

        $this->assertTrue(has($env, 'foo'));

        $foo = get($env, 'foo');

        $this->assertTrue(is_callable($foo));
        $this->assertEquals('bar', $foo());

        return $env;
    }

    /**
     * @depends testAccessors
     * @param callable $env
     */
    public function testInherit(callable $env)
    {
        $env2 = makeEnvironment([
            'baz' => function () {
                return 'bas';
            }
        ], $env);

        $this->assertTrue(has($env2, 'foo'));
        $this->assertTrue(has($env2, 'baz'));

        $foo = get($env2, 'foo');
        $baz = get($env2, 'baz');

        $this->assertEquals('bar', $foo());
        $this->assertEquals('bas', $baz());
    }
}
