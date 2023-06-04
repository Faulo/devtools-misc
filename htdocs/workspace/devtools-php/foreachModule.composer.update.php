<?php

use Slothsoft\Devtools\Update\Composer\UpdateUsingCLI;

$manager = include('src/foreachModule.php');

$manager->run(new UpdateUsingCLI());