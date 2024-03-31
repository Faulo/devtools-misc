<?php
namespace Slothsoft\Devtools\Misc\Update\Composer;

use Slothsoft\Devtools\Misc\Update\UpdateInterface;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\PHPExecutor;

class UpdateUsingCLI implements UpdateInterface {

    public function runOn(Project $project) {
        if ($project->chdir()) {
            $command = 'composer.phar update -n';
            echo $command . PHP_EOL;
            (new PHPExecutor())->execute($command);
            echo PHP_EOL;
        }
    }
}

