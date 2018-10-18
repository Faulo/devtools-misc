<?php

use Slothsoft\Devtools\Update\Git\Push;

$manager = include('src/foreachModule.php');

$manager->run(new Push());