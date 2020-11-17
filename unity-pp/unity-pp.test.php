<?php
namespace Slothsoft\Unity;

require_once __DIR__ . '/../vendor/autoload.php';

$projectPath = realpath('C:/Unity/workspace/2020WS.UnityPP.LauraWue');

$git = new GitProject($projectPath);

$branches = $git->branches();

my_dump($branches);