<?php

namespace PeacefulBit\Packet\Parser;

use function Nerd\Common\Arrays\append;
use function Nerd\Common\Arrays\toHeadTail;
use function Nerd\Common\Functional\tail;

use PeacefulBit\Packet\Exception\SyntaxException;
use PeacefulBit\Packet\Parser\Tokens\CloseBracketToken;
use PeacefulBit\Packet\Parser\Tokens\OpenBracketToken;
use PeacefulBit\Packet\Parser\Tokens\SymbolToken;
use Symfony\Component\Config\Definition\Exception\Exception;

class SyntaxTree
{
}
