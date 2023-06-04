<?php
namespace Slothsoft\Devtools;

require_once __DIR__ . '/../vendor/autoload.php';

return new ModuleManager(__DIR__ . '/../../', include ('modules.php'));