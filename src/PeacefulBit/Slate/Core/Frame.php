<?php

namespace PeacefulBit\Slate\Core;

class Frame
{
    /**
     * @var self
     */
    private $parent = null;

    /**
     * @var array
     */
    private $table = [];

    public function __construct(array $table = [], Frame $parent = null)
    {
        $this->table = $table;
        $this->parent = $parent;
    }

    /**
     * @return bool
     */
    public function isRoot(): bool
    {
        return is_null($this->parent);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        if (array_key_exists($key, $this->table)) {
            return true;
        }
        if ($this->isRoot()) {
            return false;
        }
        return $this->parent->has($key);
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function get(string $key)
    {
        if (array_key_exists($key, $this->table)) {
            return $this->table[$key];
        }
        if ($this->isRoot()) {
            return null;
        }
        return $this->parent->get($key);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, $value)
    {
        $this->table[$key] = $value;
    }

    /**
     * @param array $table
     * @return Frame
     */
    public function extend(array $table = []): Frame
    {
        return new self($table, $this);
    }

    /**
     * @param array $table
     * @return Frame
     */
    public function replace(array $table = []): Frame
    {
        $this->table = $table;
        return $this;
    }

    public function __toString()
    {
        return json_encode(array_keys($this->table));
    }
}
