<?php
namespace Slothsoft\Devtools\Update\Composer;

use Composer\Console\Application;
use Slothsoft\Devtools\Update\UpdateInterface;
use Symfony\Component\Console\Input\ArrayInput;

class DumpAutoloader implements UpdateInterface {

    public function __construct() {
        ini_set('memory_limit', '5G');
    }

    public function runOn(array $project) {
        $args = [];
        $args['command'] = 'dump-autoload';
        $args['-d'] = $project['workspaceDir'];

        $composer = new Application();
        $composer->setAutoExit(false);
        $composer->run(new ArrayInput($args));
    }
}

