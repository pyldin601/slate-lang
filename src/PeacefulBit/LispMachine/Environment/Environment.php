<?php

namespace PeacefulBit\LispMachine\Environment;

/**
 * @param array $content
 * @param null $parent
 * @return \Closure
 */
function makeEnvironment($content = [], $parent = null)
{
    return function ($caller) use ($content, $parent) {
        return $caller($content, $parent);
    };
}

/**
 * @param $environment
 * @param $symbol
 * @return mixed
 */
function has(callable $environment, $symbol)
{
    return $environment(function ($content, $parent) use ($symbol) {
        if (is_null($content)) {
            return false;
        }
        return array_key_exists($symbol, $content) || (!is_null($parent) && has($parent, $symbol));
    });
}

/**
 * @param $environment
 * @param $symbol
 * @return mixed
 */
function get(callable $environment, $symbol)
{
    return $environment(function ($content, $parent) use ($symbol) {
        if (is_null($content)) {
            return null;
        }
        return array_key_exists($symbol, $content)
            ? $content[$symbol]
            : (is_null($parent) ? null : get($parent, $symbol));
    });
}
