<?php

namespace PeacefulBit\Slate\Core;

use PeacefulBit\Slate\Exceptions\EvaluatorException;
use PeacefulBit\Slate\Parser\Parser;
use PeacefulBit\Slate\Parser\Tokenizer;

class Evaluator
{
    private static $moduleExports = [
        '\PeacefulBit\Slate\Core\Modules\Logic\export',
        '\PeacefulBit\Slate\Core\Modules\Math\export',
        '\PeacefulBit\Slate\Core\Modules\Relation\export',
        '\PeacefulBit\Slate\Core\Modules\Stdio\export',
        '\PeacefulBit\Slate\Core\Modules\Strings\export',
    ];

    /**
     * @var array
     */
    private $modules;

    /**
     * @param array $userModules
     */
    public function __construct(array $userModules = [])
    {
        $this->loadModules($userModules);
    }

    private function loadModules(array $modules)
    {
        $this->modules = array_merge($modules, ...array_map(function ($export) {
            return $export();
        }, self::$moduleExports));
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

        $frame = new Frame($this, $this->modules);

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
            throw new EvaluatorException("Module \"$name\" not found");
        }
        return $this->modules[$name];
    }
}
