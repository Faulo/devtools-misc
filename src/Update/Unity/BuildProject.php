<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Unity;

use Slothsoft\Devtools\Misc\Utils;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class BuildProject implements UpdateInterface {

    public function runOn(Project $project) {
        $command = sprintf('composer exec unity-build %s', escapeshellarg($project->workspace));

        Utils::execute($command);
    }
}

