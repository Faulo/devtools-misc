<?php
namespace Slothsoft\Devtools\Update\PHPUnit;

use Slothsoft\Devtools\Update\UpdateInterface;


class RunTests implements UpdateInterface
{
    public function runOn(array $project)
    {
        $command = 'phpunit';
        echo $command . PHP_EOL;
        passthru($command, $return);
        echo PHP_EOL;
        if ($return !== 0) {
            printf("Errors occured, please fix $project[workspaceId]!");
            die();
        }
    }
}

