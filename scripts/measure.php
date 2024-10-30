<?php
declare(strict_types = 1);

foreach ([
    __DIR__ . '/../../../autoload.php',
    __DIR__ . '/../vendor/autoload.php'
] as $file) {
    if (file_exists($file)) {
        require_once $file;
        break;
    }
}

$phpVersions = [
    // '7.0',
    // '7.2',
    '7.4',
    '8.0',
    '8.1',
    '8.2',
    '8.3'
];

$directory = escapeshellarg(dirname(__DIR__));

$commands = [
    'php --version',
    'composer --version',
    "composer -d $directory run foreach"
    // "composer -d $directory exec foreach"
];

function measure(string $cmd, int $measureCount = 1, bool $log = true): int {
    $startTime = microtime(true);

    if ($log) {
        echo "> $cmd" . PHP_EOL;
    }

    for ($i = 0; $i < $measureCount; $i ++) {
        $result = `$cmd 2>$1`;
    }

    if ($log) {
        echo $result . PHP_EOL;
    }

    $endTime = microtime(true);

    $executionTime = $endTime - $startTime;

    return (int) ceil(1000 * $executionTime / $measureCount);
}

$results = [];
foreach ($phpVersions as $version) {
    putenv("PHP_VERSION=$version");
    `composer update 2>$1`;
    $results[$version] = [];
    foreach ($commands as $command) {
        $results[$version][$command] = measure($command);
    }
}

foreach ($results as $version => $time) {
    echo "PHP v$version" . PHP_EOL;
    foreach ($commands as $command) {
        echo " $command: " . $time[$command] . "ms" . PHP_EOL;
    }
}
