<?php
namespace Slothsoft\Devtools\Misc\Update\Plastic;

use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class Pull implements UpdateInterface {

    public function runOn(Project $project) {
        if (! $project->chdir() and chdir(dirname($project->workspace))) {
            passthru("cm workspace create \"$project->name\" --server=UlissesDigital@cloud");
        }

        if ($project->chdir()) {
            passthru("cm update");
        }
    }
}

