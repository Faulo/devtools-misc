<?php
namespace Slothsoft\Devtools\Misc\Update\Plastic;

use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class Reset implements UpdateInterface {

    public function runOn(Project $project) {
        if ($project->chdir()) {
            passthru("cm undo . -r");
        }
    }
}
