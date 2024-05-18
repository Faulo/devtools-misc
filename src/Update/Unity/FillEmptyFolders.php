<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Unity;

use Slothsoft\Core\FileSystem;
use Slothsoft\Devtools\Misc\Utils;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;
use Slothsoft\Unity\UnityProjectInfo;

class FillEmptyFolders implements UpdateInterface {

    private array $searchPaths = [
        // 'Assets',
        'Packages'
    ];

    private const COMMITME_FILE = 'commitMe.txt';

    private const COMMITME_MESSAGE = <<<EOT
    This file exists so that its containing directory can be stored in version control.
    
    You can delete it as soon as another file is placed in this directory.
    EOT;

    public function runOn(Project $project) {
        if ($project->chdir()) {
            if ($unity = UnityProjectInfo::find('.', true)) {
                foreach ($this->searchPaths as $searchPath) {
                    foreach (Utils::getAllDirectories($unity->path . DIRECTORY_SEPARATOR . $searchPath) as $directory) {
                        if ($directory = realpath($directory) and ! FileSystem::scanDir($directory) and realpath($directory . '.meta')) {
                            $file = $directory . DIRECTORY_SEPARATOR . self::COMMITME_FILE;
                            echo "Creating: $file" . PHP_EOL;
                            file_put_contents($file, self::COMMITME_MESSAGE);
                        }
                    }
                }
            }
        }
    }
}

