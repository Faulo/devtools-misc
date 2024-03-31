<?php
namespace Slothsoft\Devtools\Misc\Update\Git;

use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class Pull implements UpdateInterface {

    public function runOn(Project $project) {
        if (is_dir($project->info['workspaceDir'])) {
            chdir($project->info['workspaceDir']);
            passthru('git pull');
        }
    }
}

