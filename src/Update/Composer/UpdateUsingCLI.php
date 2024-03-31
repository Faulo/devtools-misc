<?php
namespace Slothsoft\Devtools\Misc\Update\Composer;

use Slothsoft\Devtools\Misc\Update\UpdateInterface;
use Slothsoft\Devtools\Misc\Update\Project;

class UpdateUsingCLI implements UpdateInterface {

    public function runOn(Project $project) {
        if ($project->chdir()) {
            $command = 'composer update -n';
            echo $command . PHP_EOL;
            passthru($command);
            echo PHP_EOL;
        }
    }
}

