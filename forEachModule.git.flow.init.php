<?php

use Slothsoft\Devtools\Update\Git\FlowInit;

$manager = include('src/foreachModule.php');

$manager->run(new FlowInit());