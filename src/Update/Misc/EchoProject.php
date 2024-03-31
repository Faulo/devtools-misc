<?php
namespace Slothsoft\Devtools\Misc\Update\Misc;

use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class EchoProject implements UpdateInterface {

    public function runOn(Project $project) {
        var_dump($project->info);
    }
}

