<?php
namespace Slothsoft\Devtools\Update\Git;

use Slothsoft\Devtools\Update\UpdateInterface;

class FlowInit implements UpdateInterface {

    public function runOn(array $project) {
        $this->exec('git flow init -d');
    }

    private function exec(string $command) {
        $status = 0;
        passthru($command, $status);
        if ($status !== 0) {
            echo "ERROR running command '$command'." . PHP_EOL;
            die();
        }
    }
}

