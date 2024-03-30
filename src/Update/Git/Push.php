<?php
namespace Slothsoft\Devtools\Misc\Update\Git;

use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class Push implements UpdateInterface {

    public function runOn(array $project) {
        // passthru('git push --set-upstream origin develop');
        passthru('git push');
    }
}

