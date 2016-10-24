<?php

namespace PeacefulBit\Packet\Context;

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
        return array_key_exists($key, $this->content)
        || (!$this->isRoot() && $this->parent->has($key));
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function get($key)
    {
        return array_key_exists($key, $this->content) ? $this->content[$key]
             : ($this->isRoot() ? null : $this->parent->get($key));
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
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
}
