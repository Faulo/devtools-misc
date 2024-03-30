<?php



use Slothsoft\Devtools\Update\Fix\FixDeprecated;

$manager = include('src/foreachModule.php');

$manager->run(new FixDeprecated());