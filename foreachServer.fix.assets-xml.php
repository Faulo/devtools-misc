<?php


use Slothsoft\Devtools\Update\Fix\FixAssetsXml;

$manager = include('src/foreachServer.php');

$manager->run(new FixAssetsXml());