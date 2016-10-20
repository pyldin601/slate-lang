<?php

namespace PeacefulBit\LispMachine\Environment;

function makeEnvironment($content = [], $parent = null)
{
    return function ($caller) use ($content, $parent) {
        return $caller($content, $parent);
    };
}

function contains($environment, $symbol)
{
    return $environment(function ($content, $parent) use ($symbol) {
        if (is_null($content)) {
            return false;
        }
        return array_key_exists($symbol, $content) || contains($parent, $symbol);
    });
}

function get($environment, $symbol)
{
    return $environment(function ($content, $parent) use ($symbol) {
        if (is_null($content)) {
            return null;
        }
        return array_key_exists($symbol, $content)
            ? $content[$symbol]
            : get($parent, $symbol);
    });
}
