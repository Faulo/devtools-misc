<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Unity;

use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class RunTests implements UpdateInterface {

    public function runOn(Project $project) {
        $command = sprintf('composer exec unity-tests %s EditMode', escapeshellarg($project->workspace));
        echo $command . PHP_EOL;
        $return = 0;
        passthru($command, $return);
        echo PHP_EOL;
        if ($return !== 0) {
            printf("Errors occured, please fix '$project->workspace'!");
            die();
        }
    }
}

