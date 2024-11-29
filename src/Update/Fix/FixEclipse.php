<?php
namespace Slothsoft\Devtools\Misc\Update\Fix;

use Slothsoft\Devtools\Misc\Utils;
use Slothsoft\Devtools\Misc\Update\PHPExecutor;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class FixEclipse implements UpdateInterface {

    const SOURCE_PHP = 'static/php';

    const SOURCE_PACKAGIST = 'static/packagist';

    public function runOn(Project $project) {
        $version = (new PHPExecutor($project->workspace))->version;
        if ($dir = realpath(self::SOURCE_PHP . DIRECTORY_SEPARATOR . $version)) {
            echo "Copying $dir to $project->workspace" . PHP_EOL;
            Utils::copyDirectory($dir, $project->workspace);
        }

        if (isset($project->info['packagistUrl'])) {
            echo "Copying static/packagist to $project->workspace" . PHP_EOL;
            Utils::copyDirectory(self::SOURCE_PACKAGIST, $project->workspace);
        }
    }
}

