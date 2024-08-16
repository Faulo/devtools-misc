<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Plastic;

use Slothsoft\Devtools\Misc\Utils;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class Pull implements UpdateInterface {

    public function runOn(Project $project) {
        if (chdir(dirname($project->workspace))) {
            Utils::execute("cm workspace create \"$project->name\" --server=UlissesDigital@cloud");
        }

        if ($project->chdir() and is_dir('.plastic')) {
            Utils::execute("cm update");
        }
    }
}

