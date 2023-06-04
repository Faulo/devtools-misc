<?php

use Slothsoft\Devtools\Update\PHPUnit\RunTests;

$manager = include('src/foreachModule.php');

$manager->run(new RunTests());