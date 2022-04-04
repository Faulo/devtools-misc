<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools;

foreach ([
    __DIR__ . '/../../../autoload.php',
    __DIR__ . '/../vendor/autoload.php'
] as $file) {
    if (file_exists($file)) {
        require_once $file;
        break;
    }
}

$modules = ModuleManager::createSlothsoftModules('farah', 'blob', 'core', 'w3c');
$manager = new ModuleManager(__DIR__ . '/../../', $modules);

$manager->run(new Update\Fix\FixTestsCreate());