<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Unity;

use Slothsoft\Core\FileSystem;
use Slothsoft\Devtools\Misc\Utils;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;
use Slothsoft\Unity\UnityProjectInfo;

class DeleteEmptyFolders implements UpdateInterface {

    private array $searchPaths = [
        // 'Assets',
        'Packages'
    ];

    public function runOn(Project $project) {
        if ($project->chdir()) {
            if ($unity = UnityProjectInfo::find('.', true)) {
                foreach ($this->searchPaths as $searchPath) {
                    foreach (Utils::getAllDirectories($unity->path . DIRECTORY_SEPARATOR . $searchPath) as $directory) {
                        if ($directory = realpath($directory) and ! FileSystem::scanDir($directory) and $file = realpath($directory . '.meta')) {
                            Utils::delete($directory);
                            Utils::delete($file);
                        }
                    }
                }
            }
        }
    }
}

