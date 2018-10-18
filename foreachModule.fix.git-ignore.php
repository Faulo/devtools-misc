<?php

use Slothsoft\Devtools\Update\Fix\FixGitIgnore;

$manager = include('src/foreachModule.php');

$manager->run(new FixGitIgnore());