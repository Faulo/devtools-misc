<?php
namespace Slothsoft\Devtools\Misc\Update\Composer;

use Slothsoft\Devtools\Misc\Update\PHPExecutor;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class UpdateUsingCLI implements UpdateInterface {

    public function runOn(Project $project) {
        $php = new PHPExecutor();
        $php->composer('selfupdate');
        $php->composer('update', '-n');
    }
}

