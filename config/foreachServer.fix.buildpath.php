<?php


use Slothsoft\Devtools\Update\Fix\FixBuildPathFile;
use Slothsoft\Devtools\Update\Fix\FixProjectFile;
use Slothsoft\Devtools\Update\Fix\FixComposerJson;

$manager = include('src/foreachServer.php');

$manager->run(new FixComposerJson(), new FixBuildPathFile(), new FixProjectFile());