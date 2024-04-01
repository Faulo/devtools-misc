<?php
namespace Slothsoft\Devtools\Misc\Update\Git;

use Slothsoft\Devtools\Misc\Update\UpdateInterface;
use Slothsoft\Devtools\Misc\Update\Project;

class Commit implements UpdateInterface {

    private string $message;

    public function __construct(string $message) {
        $this->message = $message;
    }

    public function runOn(Project $project) {
        if ($project->chdir()) {
            passthru('git add .');
            passthru('git commit -m ' . escapeshellarg($this->message));
        }
    }
}

