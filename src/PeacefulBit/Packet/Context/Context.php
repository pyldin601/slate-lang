<?php

namespace PeacefulBit\Packet\Context;

use function Nerd\Common\Functional\tail;
use PeacefulBit\Packet\Exception\RuntimeException;

class Context
{
    /**
     * @var array
     */
    private $content = [];

    /**
     * @var null|Context
     */
    private $parent = null;

    /**
     * @param array $content
     */
    public function __construct(array $content = [])
    {
        $this->content = $content;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        $iter = tail(function ($key, Context $ctx) use (&$iter) {
            if (array_key_exists($key, $ctx->content)) {
                return true;
            }
            if ($ctx->isRoot()) {
                return false;
            }
            return $iter($key, $ctx->parent);
        });

        return $iter($key, $this);
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function get($key)
    {
        $iter = tail(function ($key, Context $ctx) use (&$iter) {
            if (array_key_exists($key, $ctx->content)) {
                return $ctx->content[$key];
            }
            if ($ctx->isRoot()) {
                return null;
            }
            return $iter($key, $ctx->parent);
        });

        return $iter($key, $this);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @throws RuntimeException
     */
    public function set($key, $value)
    {
        if (array_key_exists($key, $this->content)) {
            throw new RuntimeException("Symbol \"$key\" already defined in local context");
        }
        $this->content[$key] = $value;
    }

    /**
     * @return bool
     */
    public function isRoot()
    {
        return is_null($this->parent);
    }

    /**
     * @param array $content
     * @return Context
     */
    public function newContext(array $content = [])
    {
        $child = new Context($content);
        $child->parent = $this;
        return $child;
    }

    /**
     * @param array $content
     * @return Context
     */
    public function changeContext(array $content = [])
    {
        $this->content = $content;
        return $this;
    }
}
