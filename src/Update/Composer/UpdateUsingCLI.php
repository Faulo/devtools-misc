<?php
namespace Slothsoft\Devtools\Misc\Update\Composer;

use Slothsoft\Devtools\Misc\Update\PHPExecutor;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class UpdateUsingCLI implements UpdateInterface {

    public function runOn(Project $project) {
        if ($project->chdir()) {
            $command = 'composer.phar selfupdate';
            (new PHPExecutor())->execute($command);

            $command = 'composer.phar update -n';
            (new PHPExecutor())->execute($command);
        }
    }
}

