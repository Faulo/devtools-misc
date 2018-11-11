<?php

use Slothsoft\Devtools\Update\Fix\FixComposerJson;

$manager = include('src/foreachServer.php');

$manager->run(new FixComposerJson());