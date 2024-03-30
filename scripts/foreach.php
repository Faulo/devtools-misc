<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc;

use Slothsoft\Devtools\Misc\ProjectDatabase;
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

$ids = CLI::tokenize(array_shift($_SERVER['argv']));
$workloads = CLI::tokenize(array_shift($_SERVER['argv']));

$database = ProjectDatabase::instance();

foreach ($database->getProjects(...$ids) as $project) {
    echo $project . PHP_EOL;
}

//$modules = ModuleManager::createSlothsoftModules(...$_SERVER['argv']);
//$manager = new ModuleManager(__DIR__ . '/../../', $modules);

//$manager->run(new Update\Fix\FixTestsCreate(), new Update\Fix\FixSrcFolder(), new Update\Fix\FixDocsCreate());