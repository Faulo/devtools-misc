<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\StaticFolder;

use Slothsoft\Devtools\Misc\Update\UpdateFactory;

class StaticFolderFactory extends UpdateFactory {

    public function addCopy(string $todo, string $sourceFolder): void {
        $this->updates[$todo] = new StaticFolderUpdate(realpath($sourceFolder));
    }

    public function addCopyWithSwitch(string $todo, $folderDelegate): void {
        $this->updates[$todo] = new StaticSwitchFolder($folderDelegate);
    }

    public function addDelete(string $todo, string ...$globs): void {
        $this->updates[$todo] = new StaticDeleteUpdate(...$globs);
    }
}


