<?php
namespace Slothsoft\Devtools\Misc\Update\Git;

use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class Pull implements UpdateInterface {

    public function runOn(Project $project) {
        if ($project->chdir()) {
            passthru('git pull');
        } else {
            assert(isset($project->info['githubUrl']), "Missing 'githubUrl' for '$project'!");

            $command = sprintf('git clone %s %s', escapeshellarg($project->info['githubUrl']), escapeshellarg($project->workspace));
            passthru($command);
        }
    }
}

