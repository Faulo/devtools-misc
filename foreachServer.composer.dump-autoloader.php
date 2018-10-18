<?php

use Slothsoft\Devtools\Update\Composer\DumpAutoloader;

$manager = include('src/foreachServer.php');

$manager->run(new DumpAutoloader());