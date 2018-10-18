<?php

use Slothsoft\Devtools\Update\Git\PullSlothsoft;

$manager = include('src/foreachModule.php');

$manager->run(new PullSlothsoft());