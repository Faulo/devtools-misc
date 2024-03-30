<?php
namespace Slothsoft\Devtools\Misc\Update\Git;

use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class Reset implements UpdateInterface {

    public function runOn(array $project) {
        passthru('git reset --hard');
        passthru('git reset --hard @{u}');
    }
}

