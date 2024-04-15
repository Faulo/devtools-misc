<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Composer;

use Slothsoft\Devtools\Misc\Utils;
use Slothsoft\Devtools\Misc\Composer\ComposerManifest;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class RunTests implements UpdateInterface {

    public function runOn(Project $project) {
        if ($project->chdir()) {
            $composer = new ComposerManifest();
            $composer->load();
            if (isset($composer->data['require-dev']['phpunit/phpunit'])) {
                Utils::execute('composer exec phpunit');
            }
        }
    }
}

