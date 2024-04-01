<?php
namespace Slothsoft\Devtools\Misc\Update\Git;

use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class Push implements UpdateInterface {

    public function runOn(Project $project) {
        if ($project->chdir()) {
            // passthru('git push --set-upstream origin develop');
            passthru('git push --all');
            passthru('git push --tags');
        }
    }
}

