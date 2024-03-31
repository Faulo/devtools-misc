<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update;

abstract class UpdateFactory {

    public abstract function createUpdate(string $id): ?UpdateInterface;
}

