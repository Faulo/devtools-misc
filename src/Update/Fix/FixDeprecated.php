<?php
namespace Slothsoft\Devtools\Misc\Update\Fix;

use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class FixDeprecated implements UpdateInterface {

    private function getDeprecatedFiles(array $project): array {
        return [
            $project['workspaceDir'] . 'assets/manifest.tmp',
            $project['workspaceDir'] . 'farah/config.php',
            $project['workspaceDir'] . 'public/getPage.php',
            $project['workspaceDir'] . 'public/getAsset.php',
            $project['workspaceDir'] . 'composer.phar',
            // $project['workspaceDir'] . 'composer.lock',
            $project['workspaceDir'] . 'phpunit.bootstrap.php',
            $project['workspaceDir'] . 'build',
            $project['workspaceDir'] . 'run-tests.launch',
            $project['workspaceDir'] . 'output',
            $project['workspaceDir'] . 'node_modules',
            $project['workspaceDir'] . 'tests/autoload.php'
            // $project['workspaceDir'] . 'log',
            // $project['workspaceDir'] . 'cache',
            // $project['workspaceDir'] . 'data',
            // $project['workspaceDir'] . 'tests/autoload.php',
            // $project['workspaceDir'] . '.settings' . DIRECTORY_SEPARATOR . 'org.eclipse.php.core.prefs',
            // $project['slothsoftDir'],
        ];
    }

    public function runOn(array $project) {
        foreach ($this->getDeprecatedFiles($project) as $file) {
            if (file_exists($file)) {
                echo "\t$file" . PHP_EOL;
                exec('Recycle.exe -f ' . escapeshellarg($file));
            }
        }
    }
}

