<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Git;

use Slothsoft\Devtools\Misc\Utils;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class Pull implements UpdateInterface {

    public function runOn(Project $project) {
        if ($project->chdir() and is_dir('.git')) {
            Utils::execute('git pull');
            Utils::execute('git submodule update --init --recursive');
        } else {
            if ($project->repository) {
                $command = sprintf('git clone %s %s', escapeshellarg($project->repository), escapeshellarg($project->workspace));
                Utils::execute($command);
                if ($project->chdir() and is_dir('.git')) {
                    Utils::execute('git submodule update --init --recursive');
                }
            }
        }
    }
}

