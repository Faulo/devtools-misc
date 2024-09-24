<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\StaticFolder;

use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class StaticSwitchFolder implements UpdateInterface {

    private $folderDelegate;

    private bool $useEnvironment;

    public function __construct($folderDelegate, $useEnvironment = false) {
        $this->folderDelegate = $folderDelegate;
        $this->useEnvironment = $useEnvironment;
    }

    public function runOn(Project $project) {
        $delegate = $this->folderDelegate;
        $sourceFolder = $delegate($project);
        if ($sourceFolder and $sourceFolder = realpath($sourceFolder)) {
            (new StaticFolderUpdate($sourceFolder, $this->useEnvironment))->runOn($project);
        }
    }
}

