<?php
namespace Slothsoft\Devtools\Misc\Update\Fix;

use Slothsoft\Devtools\Misc\CLI;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;
use Slothsoft\Devtools\Misc\Update\PHPExecutor;

class FixEclipse implements UpdateInterface {

    const SOURCE_PHP = 'static/php';

    const SOURCE_PACKAGIST = 'static/packagist';

    public function runOn(Project $project) {
        $version = (new PHPExecutor($project->workspace))->version;
        if ($dir = realpath(self::SOURCE_PHP . DIRECTORY_SEPARATOR . $version)) {
            CLI::copyDirectory($dir, $project->workspace);
        }

        if (isset($project->info['packagistUrl'])) {
            CLI::copyDirectory(self::SOURCE_PACKAGIST, $project->workspace);
        }
    }
}

