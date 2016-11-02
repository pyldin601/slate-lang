<?php

namespace PeacefulBit\Slate\Core;

use PeacefulBit\Slate\Exceptions\EvaluatorException;
use PeacefulBit\Slate\Parser\Parser;
use PeacefulBit\Slate\Parser\Tokenizer;

class Evaluator
{
    /**
     * @var array
     */
    private $modules;

    /**
     * @param array $modules
     */
    public function __construct(array $modules = [])
    {
        $this->modules = $modules;
    }

    /**
     * Initialize and return root frame.
     * @return Frame
     */
    private function getRootFrame()
    {
        $frame = new Frame($this);
        $frame->importModule('@');

        return $frame;
    }

    /**
     * @param string $code
     * @return mixed
     */
    public function evaluate(string $code)
    {
        $tokenizer = new Tokenizer();
        $tokens = $tokenizer->tokenize($code);

        $parser = new Parser();
        $ast = $parser->parse($tokens);

        $frame = $this->getRootFrame();

        return $frame->valueOf($ast);
    }

    /**
     * @param string $code
     * @return mixed
     */
    public function __invoke(string $code)
    {
        return $this->evaluate($code);
    }

    /**
     * @param $name
     * @return mixed
     * @throws EvaluatorException
     */
    public function getModule($name)
    {
        if (!array_key_exists($name, $this->modules)) {
            throw new EvaluatorException("Module \"$name\" does not exist");
        }
        return $this->modules[$name];
    }
}
