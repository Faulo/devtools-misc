<?php

use Slothsoft\Devtools\Update\Fix\FixComposerJson;

$manager = include('src/foreachModule.php');

$manager->run(new FixComposerJson());