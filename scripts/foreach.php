<?php
declare(strict_types = 1);

use Slothsoft\Devtools\Misc\CLI;
use Slothsoft\Devtools\Misc\Update\ProjectDatabase;
use Slothsoft\Devtools\Misc\Update\UpdateDatabase;
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
$projects = CLI::tokenize($projects);
$projects = ProjectDatabase::instance()->getProjects(...$projects);

$updates = array_shift($_SERVER['argv']);
$updates = CLI::tokenize($updates);
$updates = UpdateDatabase::instance()->getUpdates(...$updates);

$projects->run(...$updates);