<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Unity;

use Slothsoft\Devtools\Misc\Utils;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;
use Slothsoft\Unity\UnityHub;

class BuildSolution implements UpdateInterface {

    public function runOn(Project $project) {
        $hub = UnityHub::getInstance();
        $unity = $hub->findProject($project->workspace, true);

        if ($unity and is_dir($unity->getProjectPath())) {
            $command = sprintf('composer exec unity-method %s Slothsoft.UnityExtensions.Editor.Build.Solution', escapeshellarg($unity->getProjectPath()));

            Utils::execute($command);
        }
    }
}

