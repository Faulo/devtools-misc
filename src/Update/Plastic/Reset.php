<?php
namespace Slothsoft\Devtools\Misc\Update\Plastic;

use Slothsoft\Devtools\Misc\Utils;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class Reset implements UpdateInterface {

    public function runOn(Project $project) {
        if ($project->chdir() and is_dir('.plastic')) {
            Utils::execute("cm undo . -r");

            $files = [];
            exec('cm status --private --short', $files);
            foreach ($files as $file) {
                Utils::delete($file);
            }
        }
    }
}

