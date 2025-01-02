<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Roms;

use Slothsoft\Core\FileSystem;

abstract class RomManagerBase {

    abstract function doesSupport(string $extension): bool;

    abstract function romInfo(string $inputFile): RomInfo;

    abstract function getCommand(string $command): CommandBase;

    public function run(string $command, string $input, string $output) {
        $command = $this->getCommand($command);

        $node = FileSystem::asNode($input);

        foreach ($node->getElementsByTagName('file') as $file) {
            if ($this->doesSupport($file->getAttribute('ext'))) {
                $info = $this->romInfo($file->getAttribute('path'));
                $command->do($info, $output);
            }
        }
    }
}