<?php
namespace Slothsoft\Devtools\Misc\Update\Git;

use Slothsoft\Devtools\Misc\Update\UpdateInterface;
use Slothsoft\Devtools\Misc\Update\Project;

class FlowPatch implements UpdateInterface {

    public function runOn(Project $project) {
        if ($project->chdir()) {
            $version = exec('git describe --tags --abbrev=0');
            $version = explode('.', $version);
            $version[2] ++;
            $version = implode('.', $version);

            passthru("git flow release start $version");
            passthru("git flow release finish $version -p -m \"minor fixes\"");
        }
    }
}

