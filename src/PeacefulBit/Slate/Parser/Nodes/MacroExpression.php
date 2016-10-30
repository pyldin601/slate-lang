<?php

namespace PeacefulBit\Slate\Parser\Nodes;

use function Nerd\Common\Strings\indent;

class MacroExpression extends FunctionExpression
{
    public function __toString()
    {
        $prefix = '(def-macro ';
        $suffix = ')';

        $signature = array_merge([$this->getId()], $this->getParams());
        $signatureString = '(' . implode(' ', array_map('strval', $signature)) . ')';

        $body = strval($this->getBody());

        $indentedBody = strlen($body) <= self::INLINE_THRESHOLD ? ' '  . $body : PHP_EOL . indent(2, $body);

        return $prefix . $signatureString . $indentedBody . $suffix;
    }
}
