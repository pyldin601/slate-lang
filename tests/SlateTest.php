<?php

namespace tests;

use PeacefulBit\Slate\Slate;
use PHPUnit\Framework\TestCase;

class SlateTest extends TestCase
{
    public function testProgram()
    {
        $program = file_get_contents(__DIR__ . '/fixtures/program.st');
        $this->assertTrue((new Slate)->evaluate($program));
    }
}
