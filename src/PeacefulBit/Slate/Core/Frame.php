<?php

namespace PeacefulBit\Slate\Core;

use function Nerd\Common\Arrays\toString;
use function Nerd\Common\Functional\tail;

use PeacefulBit\Slate\Exceptions\EvaluatorException;

class Frame
{
    /**
     * @var Evaluator
     */
    private $evaluator;

    /**
     * @var self
     */
    private $parent = null;

    /**
     * @var array
     */
    private $table = [];

    /**
     * @var array
     */
    private $modules = [];

    /**
     * @param Evaluator $evaluator
     * @param array $table
     * @param Frame|null $parent
     */
    public function __construct(Evaluator $evaluator, array $table = [], Frame $parent = null)
    {
        $this->evaluator = $evaluator;
        $this->table = $table;
        $this->parent = $parent;
    }

    /**
     * @param mixed $node
     * @return mixed
     */
    public function evaluate($node)
    {
//        if (!$node instanceof Nodes\NodeInterface) {
//            return $node;
//        }

//        $iter = tail(function ($node) use (&$iter) {
//            if ($node instanceof Nodes\CallExpression) {
//                return $iter($node->evaluate($this));
//            }
//            return $node;
//        });
//
//        return $iter($node->evaluate($this));
        return $node->evaluate($this);
    }

    /**
     * @param mixed $node
     * @return mixed
     */
    public function valueOf($node)
    {
//        $iter = tail(function ($node) use (&$iter) {
//            if ($node instanceof Nodes\Node) {
//                return $iter($this->evaluate($node));
//            }
//            return $node;
//        });
//
//        return $iter($node);
        return $this->evaluate($node);
    }

    /**
     * @param mixed $node
     * @return mixed
     */
    public function __invoke($node)
    {
        return $this->evaluate($node);
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
        $iter = tail(function ($key, Frame $frame) use (&$iter) {
            if (array_key_exists($key, $frame->table)) {
                return true;
            }
            if ($frame->isRoot()) {
                return false;
            }
            return $iter($key, $frame->parent);
        });

        return $iter($key, $this);
    }

    /**
     * @param string $key
     * @return mixed|null
     * @throws EvaluatorException
     */
    public function get(string $key)
    {
        $iter = tail(function ($key, Frame $frame) use (&$iter) {
            if (array_key_exists($key, $frame->table)) {
                return $frame->table[$key];
            }
            if ($frame->isRoot()) {
                throw new EvaluatorException("Symbol \"$key\" not defined");
            }
            return $iter($key, $frame->parent);
        });

        return $iter($key, $this);
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
     * @param array $path
     * @return mixed
     * @throws EvaluatorException
     */
    public function getFromModule(array $path)
    {
        $node = array_reduce($path, function ($acc, $item) {
            return (is_array($acc) && array_key_exists($item, $acc)) ? $acc[$item] : null;
        }, $this->modules);

        if (is_null($node)) {
            throw new EvaluatorException("Module not imported");
        }

        return $node;
    }

    /**
     * @param array $table
     * @return Frame
     */
    public function extend(array $table = []): Frame
    {
        return new self($this->evaluator, $table, $this);
    }

    /**
     * @param array $table
     * @return Frame
     */
    public function replace(array $table = []): Frame
    {
        $this->table = array_merge($this->table, $table);
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return toString(array_keys($this->table));
    }

    /**
     * @return Evaluator
     */
    public function getEvaluator(): Evaluator
    {
        return $this->evaluator;
    }

    /**
     * @param string $name Module name to use
     * @param null|string $alias Module alias name
     */
    public function useModule(string $name, string $alias = null)
    {
        $module = $this->getEvaluator()->getModule($name);
        $this->modules[$alias ?? $name] = $module;
    }

    /**
     * @param string $name Module name to import
     * @param array|null $functions List of functions to import
     */
    public function importModule(string $name, array $functions = null)
    {
        $module = $this->getEvaluator()->getModule($name);

        $importFunctions = $functions ?? array_keys($module);

        array_walk($importFunctions, function ($fn) use (&$module) {
            $this->set($fn, $module[$fn]);
        });
    }
}
