<?php

namespace PeacefulBit\Slate;

use PeacefulBit\Slate\Core\Evaluator;

class Slate
{
    private static $moduleExports = [
        '\PeacefulBit\Slate\Core\Modules\Base\export',
        '\PeacefulBit\Slate\Core\Modules\Math\export',
        '\PeacefulBit\Slate\Core\Modules\Relation\export',
        '\PeacefulBit\Slate\Core\Modules\Stdio\export',
        '\PeacefulBit\Slate\Core\Modules\Strings\export',
        '\PeacefulBit\Slate\Core\Modules\Lists\export'
    ];

    private function getModules()
    {
        return array_merge_recursive(...array_map(function ($export) {
            return $export();
        }, self::$moduleExports));
    }

    public function evaluate($code)
    {
        $modules = $this->getModules();

        $evaluator = new Evaluator($modules);

        return $evaluator->evaluate($code);
    }
}
