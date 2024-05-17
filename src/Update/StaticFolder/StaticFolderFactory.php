<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\StaticFolder;

use Slothsoft\Devtools\Misc\Update\UpdateFactory;

class StaticFolderFactory extends UpdateFactory {

    public function addCopy(string $todo, string $sourceFolder): StaticFolderFactory {
        $this->updates[$todo] = new StaticFolderUpdate(realpath($sourceFolder));

        return $this;
    }
}


