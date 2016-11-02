<?php

namespace PeacefulBit\Slate;

use PeacefulBit\Slate\Core\Evaluator;

class Slate
{
    private static $moduleExports = [
        '\PeacefulBit\Slate\Core\Modules\Logic\export',
        '\PeacefulBit\Slate\Core\Modules\Math\export',
        '\PeacefulBit\Slate\Core\Modules\Relation\export',
        '\PeacefulBit\Slate\Core\Modules\Stdio\export',
        '\PeacefulBit\Slate\Core\Modules\Strings\export',
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

        return $evaluator->evaluate($modules);
    }
}
