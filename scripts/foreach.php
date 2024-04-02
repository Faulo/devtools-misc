<?php
declare(strict_types = 1);

use Slothsoft\Devtools\Misc\Utils;
use Slothsoft\Devtools\Misc\Update\ProjectDatabase;
foreach ([
    __DIR__ . '/../../../autoload.php',
    __DIR__ . '/../vendor/autoload.php'
] as $file) {
    if (file_exists($file)) {
        require_once $file;
        break;
    }
}

array_shift($_SERVER['argv']);
$_SERVER['argc'] --;

if (count($_SERVER['argv']) < 1) {
    throw new \InvalidArgumentException('Needs project identifier and stuff to do!');
}

$projects = array_shift($_SERVER['argv']);
$projects = Utils::tokenize($projects);
$projects = ProjectDatabase::instance()->getProjects(...$projects);

$updates = array_shift($_SERVER['argv']);
$updates = Utils::tokenize($updates);

$projects->run(...$updates);