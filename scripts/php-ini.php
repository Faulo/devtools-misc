<?php
declare(strict_types = 1);

use Slothsoft\Core\FileSystem;
foreach ([
    __DIR__ . '/../../../autoload.php',
    __DIR__ . '/../vendor/autoload.php'
] as $file) {
    if (file_exists($file)) {
        require_once $file;
        break;
    }
}

function cleanPath(string $path): string {
    return str_replace('\\', '/', substr(realpath($path), 2));
}

$serverPath = '/Webserver';

$apachePath = realpath("$serverPath/apache-2.4");
$htdocsPath = realpath("$serverPath/htdocs");

$iniPath = realpath('php.ini');
$basePath = realpath("$iniPath/base");
$sapiPath = realpath("$iniPath/sapi");
$versionPath = realpath("$iniPath/version");
$versionSapiPath = realpath("$iniPath/version-sapi");

$releasePath = realpath("$iniPath/release");
$releasePath = $serverPath;

$versionFiles = FileSystem::scanDir($versionPath, FileSystem::SCANDIR_REALPATH);
$sapiFiles = FileSystem::scanDir($sapiPath, FileSystem::SCANDIR_REALPATH);
$baseFiles = FileSystem::scanDir($basePath, FileSystem::SCANDIR_REALPATH);

if ($serverPath = realpath($serverPath)) {
    foreach ($versionFiles as $versionFile) {
        $version = pathinfo($versionFile, PATHINFO_FILENAME);

        if ($versionPath = realpath($serverPath . DIRECTORY_SEPARATOR . $version)) {
            echo $versionPath . PHP_EOL;
            chdir($versionPath);

            foreach ($sapiFiles as $sapiFile) {
                $sapi = pathinfo($sapiFile, PATHINFO_FILENAME);

                $iniFile = sprintf('%s/%s/php-%s.ini', $releasePath, $version, $sapi);
                echo $iniFile . PHP_EOL;

                $ini = '';
                foreach ($baseFiles as $baseFile) {
                    $base = pathinfo($baseFile, PATHINFO_FILENAME);
                    $ini .= ";$base" . PHP_EOL;
                    $ini .= file_get_contents($baseFile) . PHP_EOL;
                    $ini .= PHP_EOL;
                }

                $ini .= ";$version" . PHP_EOL;
                $ini .= file_get_contents($versionFile) . PHP_EOL;
                $ini .= PHP_EOL;

                $ini .= ";$sapi" . PHP_EOL;
                $ini .= file_get_contents($sapiFile) . PHP_EOL;
                $ini .= PHP_EOL;

                $versionSapi = "$version-$sapi";
                $versionSapiFile = $versionSapiPath . DIRECTORY_SEPARATOR . "$versionSapi.ini";
                if (file_exists($versionSapiFile)) {
                    $ini .= ";$versionSapi" . PHP_EOL;
                    $ini .= file_get_contents($versionSapiFile) . PHP_EOL;
                    $ini .= PHP_EOL;
                }

                $translation = [];
                $translation['$PHP_DIRECTORY'] = cleanPath($versionPath);
                $translation['$APACHE_DIRECTORY'] = cleanPath($apachePath);
                $translation['$HTDOCS_DIRECTORY'] = cleanPath($htdocsPath);
                $ini = strtr($ini, $translation);

                if (! is_dir(dirname($iniFile))) {
                    mkdir(dirname($iniFile));
                }
                file_put_contents($iniFile, $ini);
            }

            passthru('php --version');
        }
    }
}