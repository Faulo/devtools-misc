<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Unity;

use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;
use Slothsoft\Unity\UnityHub;
use Slothsoft\Unity\ExecutionError;

class BuildProject implements UpdateInterface {

    private string $path;

    private string $target;

    public function __construct(string $path, string $target) {
        $this->path = $path;
        $this->target = $target;
    }

    public function runOn(Project $project) {
        $hub = UnityHub::getInstance();
        $unity = $hub->findProject($project->workspace, true);

        if ($unity and chdir($unity->getProjectPath())) {
            try {
                $process = $unity->build($this->target, $this->path);
                $this->echoLines(' > ' . $process->getCommandLine(), $process->getOutput(), $process->getErrorOutput());
            } catch (ExecutionError $error) {
                $this->echoLines($error, $error->getStdOut(), $error->getStdErr());
            }
        }
    }

    private function echoLines(string ...$lines) {
        foreach ($lines as $line) {
            $line = (string) $line;
            if (strlen($line)) {
                echo $line . PHP_EOL;
            }
        }
    }
}

