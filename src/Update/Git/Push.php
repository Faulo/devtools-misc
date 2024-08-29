<?php
namespace Slothsoft\Devtools\Misc\Update\Git;

use Slothsoft\Devtools\Misc\Utils;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class Push implements UpdateInterface {

    public function runOn(Project $project) {
        if ($project->chdir() and is_dir('.git')) {
            // passthru('git push --set-upstream origin develop');
            Utils::execute('git push --all');
            Utils::execute('git push --tags');
        }
    }
}

