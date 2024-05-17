<?php
namespace Slothsoft\Devtools\Misc\Update\Plastic;

use Slothsoft\Core\FileSystem;
use Slothsoft\Devtools\Misc\Utils;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class Reset implements UpdateInterface {

    public function runOn(Project $project) {
        if ($project->chdir()) {
            Utils::execute("cm undo . -r");

            $files = [];
            exec('cm status --private --short', $files);
            foreach ($files as $file) {
                if ($file = realpath($file)) {
                    echo "Deleting private file '$file'" . PHP_EOL;
                    if (is_dir($file)) {
                        FileSystem::removeDir($file);
                    } else {
                        unlink($file);
                    }
                }
            }
        }
    }
}

