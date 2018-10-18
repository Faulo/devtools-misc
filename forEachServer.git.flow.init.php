<?php

use Slothsoft\Devtools\Update\Git\FlowInit;

$manager = include('src/foreachServer.php');

$manager->run(new FlowInit());