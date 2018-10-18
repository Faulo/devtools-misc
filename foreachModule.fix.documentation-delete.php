<?php


use Slothsoft\Devtools\Update\Fix\FixDocsDelete;

$manager = include('src/foreachModule.php');

$manager->run(new FixDocsDelete());