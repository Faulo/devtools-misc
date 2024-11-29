<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update;

class StubUpdate implements UpdateInterface {

    public function runOn(Project $project) {}
}

