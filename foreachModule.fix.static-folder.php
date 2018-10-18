<?php



use Slothsoft\Devtools\Update\Fix\FixStaticFolder;

$manager = include('src/foreachModule.php');

$manager->run(new FixStaticFolder());