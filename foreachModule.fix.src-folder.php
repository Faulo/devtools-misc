<?php


use Slothsoft\Devtools\Update\Fix\FixSrcFolder;

$manager = include('src/foreachModule.php');

$manager->run(new FixSrcFolder());