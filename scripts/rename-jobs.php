<?php
declare(strict_types = 1);

use Slothsoft\Devtools\Misc\Utils;
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
    Rename Deployment files to Prerelease.
            
    Usage:
        composer run devops "path/to/jobs" "search" "replace"
    
    EOT;
    return;
}

$path = array_shift($_SERVER['argv']);
$search = array_shift($_SERVER['argv']);
$replace = array_shift($_SERVER['argv']);

foreach (Utils::getAllDirectories($path) as $old) {
    $i = 0;
    $new = str_replace($search, $replace, $old, $i);
    if ($i !== 0) {
        rename($old, $new);
    }

    $file = $new . DIRECTORY_SEPARATOR . 'config.xml';
    if ($file = realpath($file)) {
        $xml = file_get_contents($file);
        $xml = str_replace($search, $replace, $xml, $i);
        if ($i !== 0) {
            file_put_contents($file, $xml);
        }
    }
}