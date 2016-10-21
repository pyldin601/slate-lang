<?php

namespace PeacefulBit\LispMachine\Tree;

const TYPE_SYMBOL       = 301; // variable name or number constant
const TYPE_STRING       = 302; // "any text in double quotes"
const TYPE_EXPRESSION   = 303; // (foo bar baz)
const TYPE_SEQUENCE     = 304; // (foo bar baz) (def n 10)

const NODE_ID           = '$$node$$';

/**
 * @param $type
 * @param $value
 * @return array
 */
function node($type, $value)
{
    return [NODE_ID, $type, $value];
}

/**
 * @param $node
 * @return bool
 */
function isNode($node)
{
    return is_array($node) && sizeof($node) == 3 && $node[0] == NODE_ID;
}

/**
 * @param $node
 * @return mixed
 */
function typeOf($node)
{
    return $node[1];
}

/**
 * @param $node
 * @return mixed
 */
function valueOf($node)
{
    return $node[2];
}
