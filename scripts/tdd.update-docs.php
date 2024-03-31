<?php
declare(strict_types = 1);

use Slothsoft\Devtools\Misc\Update\ModuleManager;
use Slothsoft\Devtools\Misc\Update\Fix\FixDocsCreate;

foreach ([
    __DIR__ . '/../../../autoload.php',
    __DIR__ . '/../vendor/autoload.php'
] as $file) {
    if (file_exists($file)) {
        require_once $file;
        break;
    }
}

array_shift($_SERVER['argv']);
$_SERVER['argc'] --;

if (! count($_SERVER['argv'])) {
    throw new \InvalidArgumentException('Needs at least 1 module name!');
}

$modules = ModuleManager::createSlothsoftModules(...$_SERVER['argv']);
$manager = new ModuleManager(__DIR__ . '/../../', $modules);

$manager->run(new FixDocsCreate());