<?php


use Slothsoft\Devtools\Update\Fix\FixDocsCreate;

$manager = include('src/foreachModule.php');

$manager->run(new FixDocsCreate());