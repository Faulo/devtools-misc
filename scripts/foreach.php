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
    echo <<<'EOT'
    Run custom scripts on a bunch of projects.
            
    Usage:
        composer run foreach "group-id project-id" "do-this do-that"
    
    
    EOT;

    echo 'Available Groups:' . PHP_EOL;
    foreach (ProjectDatabase::instance()->getAllGroups() as $group) {
        echo "    $group->id" . PHP_EOL;
    }
    echo PHP_EOL;

    echo 'Available Todos:' . PHP_EOL;
    foreach (ProjectDatabase::instance()->getAllUpdateKeys() as $update) {
        echo "    $update" . PHP_EOL;
    }
    return;
}

$projects = array_shift($_SERVER['argv']);
$projects = Utils::tokenize($projects);
$projects = ProjectDatabase::instance()->getProjects(...$projects);

$updates = array_shift($_SERVER['argv']);
$updates = Utils::tokenize($updates);

$projects->run(...$updates);