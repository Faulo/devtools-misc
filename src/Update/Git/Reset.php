<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Git;

use Slothsoft\Devtools\Misc\Utils;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class Reset implements UpdateInterface {

    public function runOn(Project $project) {
        if ($project->chdir() and is_dir('.git')) {
            Utils::execute('git reset --hard');
            Utils::execute('git clean -f -d');
        }
    }
}

