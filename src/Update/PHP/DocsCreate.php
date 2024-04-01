<?php
namespace Slothsoft\Devtools\Misc\Update\PHP;

use Slothsoft\Devtools\Misc\Update\PHPExecutor;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class DocsCreate implements UpdateInterface {

    public function runOn(Project $project) {
        if ($project->chdir() and file_exists('phpdoc.dist.xml')) {
            $php = new PHPExecutor();
            if ($documentor = realpath(dirname($php->executable) . '/phpDocumentor.phar')) {
                $command = sprintf('%s --no-interaction run', escapeshellarg($documentor));
                $php->execute($command);
            }
        }
    }
}

