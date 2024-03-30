<?php

use Slothsoft\Devtools\Update\Git\Release;

$manager = include('src/forOneModule.php');

$manager->run(new Release());