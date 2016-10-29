<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 29.10.16
 * Time: 21:07
 */

namespace PeacefulBit\Slate\Ast;


class Identifier extends Node
{
    /**
     * @var string
     */
    private $name;

    /**
     * @param $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
