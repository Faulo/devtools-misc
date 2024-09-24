<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\StaticFolder;

use Slothsoft\Devtools\Misc\Update\UpdateFactory;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class StaticFolderFactory extends UpdateFactory {

    public function addCopy(string $todo, string $sourceFolder, bool $create = true, bool $useEnvironment = false): void {
        if (! is_dir($sourceFolder)) {
            if ($create) {
                mkdir($sourceFolder, 0777, true);
            } else {
                throw new FileNotFoundException(null, 0, null, $sourceFolder);
            }
        }

        $this->updates[$todo] = new StaticFolderUpdate(realpath($sourceFolder), $useEnvironment);
    }

    public function addCopyWithSwitch(string $todo, $folderDelegate, bool $useEnvironment = false): void {
        $this->updates[$todo] = new StaticSwitchFolder($folderDelegate, $useEnvironment);
    }

    public function addDelete(string $todo, string ...$globs): void {
        $this->updates[$todo] = new StaticDeleteUpdate(...$globs);
    }
}


