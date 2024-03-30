<?php


use Slothsoft\Devtools\Update\Fix\FixBuildPathFile;
use Slothsoft\Devtools\Update\Fix\FixComposerJson;
use Slothsoft\Devtools\Update\Fix\FixProjectFile;

$manager = include('src/foreachModule.php');

$manager->run(new FixComposerJson(), new FixBuildPathFile(), new FixProjectFile());