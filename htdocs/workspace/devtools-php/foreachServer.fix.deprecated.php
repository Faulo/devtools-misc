<?php



use Slothsoft\Devtools\Update\Fix\FixDeprecated;

$manager = include('src/foreachServer.php');

$manager->run(new FixDeprecated());