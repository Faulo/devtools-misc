<?php

use Slothsoft\Devtools\Update\Git\Push;

$manager = include('src/foreachServer.php');

$manager->run(new Push());