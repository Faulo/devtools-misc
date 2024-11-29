<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Git;

use Slothsoft\Devtools\Misc\Utils;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;
use Slothsoft\Devtools\Misc\Update\Project;

class FlowInit implements UpdateInterface {

    public function runOn(Project $project) {
        if ($project->chdir() and is_dir('.git')) {
            Utils::execute('git flow init -d -f');
            Utils::execute('git merge master');
        }
    }
}

