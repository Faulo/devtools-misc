<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\StaticFolder;

use Slothsoft\Devtools\Misc\Utils;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class StaticDeleteUpdate implements UpdateInterface {

    private array $globs;

    public function __construct(string ...$globs) {
        $this->globs = $globs;
    }

    public function runOn(Project $project) {
        if ($project->chdir()) {
            foreach ($this->globs as $glob) {
                foreach (glob($glob, GLOB_NOSORT) as $file) {
                    Utils::delete($file);
                }
            }
        }
    }
}

