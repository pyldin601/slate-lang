<?php

namespace PeacefulBit\Slate\Parser\Nodes;

interface CallableNode
{
    public function call($arguments);
}
