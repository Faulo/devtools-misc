<?php

use Slothsoft\Devtools\ModuleManager;

require_once __DIR__ . '/../vendor/autoload.php';

return new ModuleManager(
    __DIR__ . '/../../',
    include('modules.php')
);