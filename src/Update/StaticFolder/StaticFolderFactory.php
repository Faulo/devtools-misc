<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\StaticFolder;

use Slothsoft\Devtools\Misc\Update\UpdateFactory;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class StaticFolderFactory extends UpdateFactory {

    public function addCopy(string $todo, string $sourceFolder): void {
        if (! is_dir($sourceFolder)) {
            throw new FileNotFoundException(null, 0, null, $sourceFolder);
        }

        $this->updates[$todo] = new StaticFolderUpdate(realpath($sourceFolder));
    }

    public function addCopyWithSwitch(string $todo, $folderDelegate): void {
        $this->updates[$todo] = new StaticSwitchFolder($folderDelegate);
    }

    public function addDelete(string $todo, string ...$globs): void {
        $this->updates[$todo] = new StaticDeleteUpdate(...$globs);
    }
}


