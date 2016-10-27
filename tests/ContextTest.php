<?php

namespace tests;

use PeacefulBit\Packet\Context\Context;
use PeacefulBit\Packet\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;

class EnvironmentTest extends TestCase
{
    public function testCreateEnvironment()
    {
        $ctx = new Context();
        $this->assertFalse($ctx->has('foo'));
        $this->assertNull($ctx->get('foo'));
    }

    public function testAccessors()
    {
        $ctx = new Context([
            'foo' => function () {
                return 'bar';
            }
        ]);

        $this->assertTrue($ctx->has('foo'));

        $foo = $ctx->get('foo');

        $this->assertTrue(is_callable($foo));
        $this->assertEquals('bar', $foo());

        return $ctx;
    }

    /**
     * @depends testAccessors
     * @param Context $ctx
     * @return Context
     */
    public function testInherit(Context $ctx)
    {
        $ctx2 = $ctx->newContext([
            'baz' => function () {
                return 'bas';
            }
        ]);

        $this->assertTrue($ctx2->has('foo'));
        $this->assertTrue($ctx2->has('baz'));

        $foo = $ctx2->get('foo');
        $baz = $ctx2->get('baz');

        $this->assertEquals('bar', $foo());
        $this->assertEquals('bas', $baz());

        return $ctx2;
    }

    /**
     * @depends testAccessors
     * @param Context $ctx
     * @return Context
     */
    public function testChange(Context $ctx)
    {
        $ctx2 = $ctx->changeContext(['a' => 'b']);

        $this->assertTrue($ctx2->has('a'));
        $this->assertSame($ctx2, $ctx);
    }

    /**
     * @depends testInherit
     * @param Context $ctx
     * @expectedException \PeacefulBit\Packet\Exception\RuntimeException
     */
    public function testOverwrite(Context $ctx)
    {
        $ctx->set('baz', null);
    }
}
