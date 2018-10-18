<?php


use Slothsoft\Devtools\Update\Fix\FixBuildPathFile;
use Slothsoft\Devtools\Update\Fix\FixComposerJson;
use Slothsoft\Devtools\Update\Fix\FixDeprecated;
use Slothsoft\Devtools\Update\Fix\FixGitIgnore;
use Slothsoft\Devtools\Update\Fix\FixProjectFile;
use Slothsoft\Devtools\Update\Fix\FixSrcFolder;
use Slothsoft\Devtools\Update\Fix\FixStaticFolder;

$manager = include('src/foreachModule.php');

$manager->run(
    new FixComposerJson()
    ,new FixStaticFolder() 
    ,new FixSrcFolder()
    ,new FixGitIgnore()
    ,new FixDeprecated()
    ,new FixBuildPathFile()
    ,new FixProjectFile()
);