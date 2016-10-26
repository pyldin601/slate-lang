<?php

namespace PeacefulBit\Packet\Modules\Logic;

use function Nerd\Common\Arrays\all;
use function Nerd\Common\Arrays\any;
use PeacefulBit\Packet\Exception\RuntimeException;
use PeacefulBit\Packet\Nodes\NativeNode;
use PeacefulBit\Packet\Visitors\NodeCalculatorVisitor;

function export()
{
    return [
        'or' => new NativeNode('or', function (NodeCalculatorVisitor $visitor, array $arguments) {
            $predicate = function ($item) use ($visitor) {
                $visit = $visitor->getVisitor($item);
                return !!$visit($item);
            };
            return any($arguments, $predicate);
        }),
        'and' => new NativeNode('and', function (NodeCalculatorVisitor $visitor, array $arguments) {
            $predicate = function ($item) use ($visitor) {
                $visit = $visitor->getVisitor($item);
                return !!$visit($item);
            };
            return all($arguments, $predicate);
        }),
        'not' => new NativeNode('not', function (NodeCalculatorVisitor $visitor, array $arguments) {
            return all($arguments, function ($argument) use ($visitor) {
                $visit = $visitor->getVisitor($argument);
                return !$visit($argument);
            });
        }),
        'if' => new NativeNode('if', function (NodeCalculatorVisitor $visitor, array $arguments) {
            if (sizeof($arguments) != 3) {
                throw new RuntimeException("Function 'if' accepts only three arguments");
            }
            list ($exp, $cons, $alt) = $arguments;
            $visitExp = $visitor->getVisitor($exp);
            $visitCons = $visitor->getVisitor($cons);
            $visitAlt = $visitor->getVisitor($alt);
            return $visitExp($exp) ? $visitCons($cons) : $visitAlt($alt);
        }),
        'unless' => new NativeNode('unless', function (NodeCalculatorVisitor $visitor, array $arguments) {
            if (sizeof($arguments) != 3) {
                throw new RuntimeException("Function 'unless' accepts only three arguments");
            }
            list ($exp, $cons, $alt) = $arguments;
            $visitExp = $visitor->getVisitor($exp);
            $visitCons = $visitor->getVisitor($cons);
            $visitAlt = $visitor->getVisitor($alt);
            return $visitExp($exp) ? $visitAlt($alt) : $visitCons($cons);
        })
    ];
}
