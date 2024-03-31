<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update;

interface UpdateInterface {

    public function runOn(Project $project);
}

