<?php
namespace Slothsoft\Devtools;

require_once __DIR__ . '/vendor/autoload.php';

use Slothsoft\Devtools\Update\Fix\FixDocsCreate;

$modules = ModuleManager::createSlothsoftModules('schema', 'farah', 'blob', 'core', 'w3c', 'unity');
$manager = new ModuleManager(__DIR__ . '/../', $modules);

$manager->run(new FixDocsCreate());