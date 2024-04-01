<?php
namespace Slothsoft\Devtools\Misc\Update\Git;

use Slothsoft\Devtools\Misc\Update\UpdateInterface;
use Slothsoft\Devtools\Misc\Update\Project;

class FlowInit implements UpdateInterface {

    public function runOn(Project $project) {
        if ($project->chdir()) {
            passthru('git flow init -d -f');
            passthru('git merge master');
        }
    }
}

