<?php

namespace PeacefulBit\Slate\Parser\Nodes;

use function Nerd\Common\Strings\indent;

use PeacefulBit\Slate\Core\Frame;

class FunctionExpression extends LambdaExpression
{
    /**
     * @var string
     */
    private $id;

    /**
     * @param string $id
     * @param array $params
     * @param Node $body
     */
    public function __construct(string $id, array $params, Node $body)
    {
        $this->id = $id;

        parent::__construct($params, $body);
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $prefix = '(def ';
        $suffix = ')';

        $signature = array_merge([$this->getId()], $this->getParams());
        $signatureString = '(' . implode(' ', array_map('strval', $signature)) . ')';

        $body = strval($this->getBody());

        $indentedBody = strlen($body) <= self::INLINE_THRESHOLD ? ' '  . $body : PHP_EOL . indent(2, $body);

        return $prefix . $signatureString . $indentedBody . $suffix;
    }

    /**
     * @param Frame $frame
     * @return null
     */
    public function evaluate(Frame $frame)
    {
        $frame->set($this->getId(), $this);

        return null;
    }
}
