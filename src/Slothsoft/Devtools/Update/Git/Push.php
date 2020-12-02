<?php
namespace Slothsoft\Devtools\Update\Git;

use Slothsoft\Devtools\Update\UpdateInterface;

class Push implements UpdateInterface {

    public function runOn(array $project) {
        // passthru('git push --set-upstream origin develop');
        passthru('git push');
    }
}

