<?php

namespace PeacefulBit\Slate\Ast;

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
}
