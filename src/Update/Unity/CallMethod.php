<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Unity;

use Slothsoft\Devtools\Misc\Utils;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class CallMethod implements UpdateInterface {

    private $method;

    public function __construct(string $method) {
        $this->method = $method;
    }

    public function runOn(Project $project) {
        $command = sprintf('composer exec unity-method %s %s', escapeshellarg($project->workspace), escapeshellarg($this->method));

        Utils::execute($command);
    }
}

