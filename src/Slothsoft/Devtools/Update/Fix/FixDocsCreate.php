<?php
namespace Slothsoft\Devtools\Update\Fix;

use Slothsoft\Devtools\Update\UpdateInterface;

class FixDocsCreate implements UpdateInterface {

    public function runOn(array $project) {
        $version = '2.9.0';
        // $version = '3.0.0-alpha1';
        if (is_file($project['workspaceDir'] . 'phpdoc.dist.xml')) {
            $command = sprintf('C:/Webserver/php-7.0/php.exe %s run', escapeshellarg(realpath("../devtools/bin/phpDocumentor-$version.phar")));
            echo $command . PHP_EOL;
            passthru($command);
        }
    }
}

