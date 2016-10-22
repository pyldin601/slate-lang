<?php

namespace PeacefulBit\Util;

function tail($fn)
{
    $underCall = false;
    $pool = [];

    return function (...$args) use (&$fn, &$underCall, &$pool) {
        $result = null;
        $pool[] = $args;

        if (!$underCall) {
            $underCall = true;
            while ($pool) {
                $head = array_shift($pool);
                $result = $fn(...$head);
            }
            $underCall = false;
        }

        return $result;
    };
}
