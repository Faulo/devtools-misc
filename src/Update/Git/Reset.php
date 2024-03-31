<?php
namespace Slothsoft\Devtools\Misc\Update\Git;

use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class Reset implements UpdateInterface {

    public function runOn(Project $project) {
        if ($project->chdir()) {
            passthru('git reset --hard');
        }
    }
}

