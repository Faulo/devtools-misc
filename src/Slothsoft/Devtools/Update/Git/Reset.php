<?php
namespace Slothsoft\Devtools\Update\Git;

use Slothsoft\Devtools\Update\UpdateInterface;


class Reset implements UpdateInterface
{
    public function runOn(array $project)
    {
        passthru('git reset --hard');
        passthru('git reset --hard @{u}');
    }
}

