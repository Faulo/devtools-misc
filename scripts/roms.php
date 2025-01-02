<?php
declare(strict_types = 1);

use Slothsoft\Devtools\Misc\Roms\GameCubeManager;
use Slothsoft\Devtools\Misc\Roms\Nintendo64Manager;

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

if (count($_SERVER['argv']) < 3) {
    echo <<<'EOT'
    ROM image converter.
    
    Usage:
        composer run roms gc|n64 convert|pack|store "path/to/input" "path/to/output"
    
    
    EOT;

    return;
}

$manager = array_shift($_SERVER['argv']);
switch ($manager) {
    case 'n64':
        $manager = new Nintendo64Manager();
        break;
    case 'gc':
        $manager = new GameCubeManager();
        break;
    default:
        die("Invalid manager: '$manager'");
}

$command = array_shift($_SERVER['argv']);

$input = array_shift($_SERVER['argv']);

if (! realpath($input)) {
    die("Invalid input path: '$input'");
}

$input = realpath($input);

$output = array_shift($_SERVER['argv']);

if (! realpath($output)) {
    die("Invalid output path: '$output'");
}

$output = realpath($output);

$manager->run($command, $input, $output);
