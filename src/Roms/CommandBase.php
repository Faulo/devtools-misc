<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Roms;

abstract class CommandBase {

    public abstract function do(RomInfo $input, string $output): void;
}
