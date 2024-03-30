<?php
namespace Slothsoft\Devtools\Misc\Update\Composer;

use Composer\Console\Application;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;
use Symfony\Component\Console\Input\ArrayInput;

class UpdateUsingAPI implements UpdateInterface {

    public function __construct() {
        ini_set('memory_limit', '5G');
    }

    public function runOn(array $project) {
        $args = [];
        $args['command'] = 'update';
        $args['--classmap-authoritative'] = true;
        $args['-n'] = true;
        $args['-d'] = $project['workspaceDir'];

        $composer = new Application();
        $composer->setAutoExit(false);
        $composer->run(new ArrayInput($args));
    }
}

