<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\StaticFolder;

use Slothsoft\Devtools\Misc\Utils;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class StaticFolderUpdate implements UpdateInterface {

    private string $sourceFolder;

    public function __construct(string $sourceFolder) {
        $this->sourceFolder = $sourceFolder;
    }

    public function runOn(Project $project) {
        if ($project->chdir()) {
            Utils::copyDirectory($this->sourceFolder, $project->workspace);
        }
    }
}

