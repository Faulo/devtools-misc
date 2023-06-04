<?php
namespace Slothsoft\Devtools\Update\Composer;

use Slothsoft\Devtools\Update\UpdateInterface;

class UpdateUsingCLI implements UpdateInterface {

    public function runOn(array $project) {
        $command = 'composer update -n';
        echo $command . PHP_EOL;
        passthru($command);
        echo PHP_EOL;
    }
}

