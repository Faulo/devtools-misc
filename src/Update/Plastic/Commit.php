<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Plastic;

use Slothsoft\Devtools\Misc\Utils;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class Commit implements UpdateInterface {

    private string $message;

    public function __construct(string $message) {
        $this->message = $message;
    }

    public function runOn(Project $project) {
        if ($project->chdir() and is_dir('.plastic')) {
            Utils::execute('cm add . -R');
            Utils::execute('cm ci --all -c=' . escapeshellarg($this->message));
        }
    }
}

