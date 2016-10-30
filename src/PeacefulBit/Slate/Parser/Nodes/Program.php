<?php

namespace PeacefulBit\Slate\Parser\Nodes;

class Program extends Node
{
    private $body;

    /**
     * @param $body
     */
    public function __construct($body)
    {
        $this->body = $body;
    }

    /**
     * @return mixed
     */
    private function getBody()
    {
        return $this->body;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode(PHP_EOL, array_map('strval', $this->getBody()));
    }
}
