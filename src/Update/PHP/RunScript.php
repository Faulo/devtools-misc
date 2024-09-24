<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\PHP;

use Slothsoft\Devtools\Misc\Utils;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class RunScript implements UpdateInterface {

    private string $script;

    public function __construct(string $script) {
        $this->script = $script;
    }

    public function runOn(Project $project) {
        if (! $project->chdir()) {
            return;
        }

        if (is_file($this->script)) {
            Utils::execute($this->script);
        }
    }
}

