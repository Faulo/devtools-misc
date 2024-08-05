<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Git;

use Slothsoft\Devtools\Misc\Utils;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class Commit implements UpdateInterface {

    private string $message;

    public function __construct(string $message) {
        $this->message = $message;
    }

    public function runOn(Project $project) {
        if ($project->chdir()) {
            Utils::execute('git add --renormalize .');
            Utils::execute('git commit -m ' . escapeshellarg($this->message));
        }
    }
}

