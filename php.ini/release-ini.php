<?php
use Slothsoft\Core\FileSystem;

require_once __DIR__ . '/../vendor/autoload.php';

$serverPath = 'C:/Webserver';

$apachePath = realpath("$serverPath/apache-2.4");
$htdocsPath = realpath("$serverPath/htdocs");

$basePath = realpath('base');
$sapiPath = realpath('sapi');
$versionPath = realpath('version');

$releasePath = realpath('release');

$versionFiles = FileSystem::scanDir($versionPath, FileSystem::SCANDIR_REALPATH);
$sapiFiles = FileSystem::scanDir($sapiPath, FileSystem::SCANDIR_REALPATH);
$baseFiles = FileSystem::scanDir($basePath, FileSystem::SCANDIR_REALPATH);

if ($serverPath = realpath($serverPath)) {
    foreach ($versionFiles as $versionFile) {
        $version = pathinfo($versionFile, PATHINFO_FILENAME);
        
        if ($versionPath = realpath($serverPath . DIRECTORY_SEPARATOR . $version)) {
            echo $versionPath . PHP_EOL;
            
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
                
                $translation = [];
                $translation['$PHP_DIRECTORY'] = $versionPath;
                $translation['$APACHE_DIRECTORY'] = $apachePath;
                $translation['$HTDOCS_DIRECTORY'] = $htdocsPath;
                $ini = strtr($ini, $translation);
                
                if (!is_dir(dirname($iniFile))) {
                    mkdir(dirname($iniFile));
                }
                file_put_contents($iniFile, $ini);
            }
        }
    }
}