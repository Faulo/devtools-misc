<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\PHP;

use Slothsoft\Devtools\Misc\Update\PHPExecutor;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class DocsCreate implements UpdateInterface {

    public function runOn(Project $project) {
        if ($project->chdir() and is_file('phpdoc.xml')) {
            $php = new PHPExecutor();
            if ($documentor = realpath(dirname($php->executable) . '/phpDocumentor.phar')) {
                $php->vexecute($documentor, '--no-interaction', 'run');
            }
        }
    }
}

