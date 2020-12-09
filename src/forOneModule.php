<?php
namespace Slothsoft\Devtools;

require_once __DIR__ . '/../vendor/autoload.php';

echo 'module to process: ';
$moduleName = stream_get_line(STDIN, 1024, PHP_EOL);

foreach (include ('modules.php') as $module) {
    if ($module['name'] === $moduleName) {
        return new ModuleManager(__DIR__ . '/../../', [
            $module
        ]);
    }
}

throw new \Exception("Module '$moduleName' not found.");