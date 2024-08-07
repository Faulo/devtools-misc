<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\StaticFolder;

use Slothsoft\Devtools\Misc\Utils;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class StaticSwitchFolder implements UpdateInterface {

    private $folderDelegate;

    public function __construct($folderDelegate) {
        $this->folderDelegate = $folderDelegate;
    }

    public function runOn(Project $project) {
        $delegate = $this->folderDelegate;
        $sourceFolder = $delegate($project);
        if ($sourceFolder and $sourceFolder = realpath($sourceFolder)) {
            Utils::copyDirectory($sourceFolder, $project->workspace);
        }
    }
}

