<?php

use Slothsoft\Devtools\Update\Composer\DumpAutoloader;

$manager = include('src/foreachModule.php');

$manager->run(new DumpAutoloader());