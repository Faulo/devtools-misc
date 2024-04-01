<?php
namespace Slothsoft\Devtools\Misc\Update\Composer;

use Composer\Console\Application;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Slothsoft\Devtools\Misc\Update\Project;

class DumpAutoloader implements UpdateInterface {

    public function __construct() {
        ini_set('memory_limit', '5G');
    }

    public function runOn(Project $project) {
        $args = [];
        $args['command'] = 'dump-autoload';
        $args['-d'] = $project->workspace;

        $composer = new Application();
        $composer->setAutoExit(false);
        $composer->run(new ArrayInput($args));
    }
}

