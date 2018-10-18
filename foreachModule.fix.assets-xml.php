<?php


use Slothsoft\Devtools\Update\Fix\FixAssetsXml;

$manager = include('src/foreachModule.php');

$manager->run(new FixAssetsXml());