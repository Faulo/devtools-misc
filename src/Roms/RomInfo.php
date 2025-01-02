<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Roms;

class RomInfo {

    public string $realpath;

    public string $name;

    public function __construct(string $realpath, string $name) {
        $this->realpath = $realpath;
        $this->name = $name;
    }
}