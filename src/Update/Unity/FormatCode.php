<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Unity;

use Slothsoft\Devtools\Misc\Utils;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class FormatCode implements UpdateInterface {

    public function runOn(Project $project) {
        if ($project->chdir()) {
            if (! file_exists('.editorconfig')) {
                trigger_warning("Project '$project' does not contain an .editorconfig!");
            }

            foreach (glob('*.sln') as $solution) {
                $command = sprintf('dotnet format %s --exclude Library Assets/Plugins', escapeshellarg($solution));
                Utils::execute($command);
            }
        }
    }
}

