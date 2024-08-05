<?php
namespace Slothsoft\Devtools\Misc\Update\Git;

use Slothsoft\Devtools\Misc\Utils;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class Pull implements UpdateInterface {

    public function runOn(Project $project) {
        if ($project->chdir()) {
            Utils::execute('git pull');
        } else {
            assert(! ! $project->repository, "Missing 'repository' for '$project'!");

            $command = sprintf('git clone %s %s', escapeshellarg($project->repository), escapeshellarg($project->workspace));
            Utils::execute($command);
        }
    }
}

