<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\StaticFolder;

use Slothsoft\Devtools\Misc\Utils;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class StaticFolderUpdate implements UpdateInterface {

    private string $sourceFolder;

    private bool $useEnvironment;

    public function __construct($sourceFolder, $useEnvironment = false) {
        $this->sourceFolder = $sourceFolder;
        $this->useEnvironment = $useEnvironment;
    }

    public function runOn(Project $project) {
        if ($project->chdir()) {
            Utils::copyDirectory($this->sourceFolder, $project->workspace);
            if ($this->useEnvironment and $_ENV) {
                $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->sourceFolder));
                $files = [];
                foreach ($iterator as $file) {
                    if ($file->isFile()) {
                        $path = $file->getPathname();
                        $files[$path] = str_replace($this->sourceFolder, $project->workspace, $path);
                    }
                }

                $env = [];
                foreach ($_ENV as $key => $val) {
                    $env['$' . $key] = $val;
                }

                foreach ($files as $source => $target) {
                    $old = file_get_contents($source);
                    $new = strtr($old, $env);
                    if ($old !== $new) {
                        file_put_contents($target, $new);
                    }
                }
            }
        }
    }
}

