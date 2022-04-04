<?php
declare(strict_types = 1);
require_once __DIR__ . '/../vendor/autoload.php';

use Slothsoft\Devtools\ModuleManager;
use Slothsoft\Devtools\Update\Fix\FixTestsCreate;

$modules = ModuleManager::createSlothsoftModules('farah', 'blob', 'core', 'w3c');
$manager = new ModuleManager(__DIR__ . '/../../', $modules);

$manager->run(new FixTestsCreate());