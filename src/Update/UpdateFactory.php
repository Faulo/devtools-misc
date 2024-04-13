<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update;

abstract class UpdateFactory {

    public array $updates = [];

    public function createUpdate(string $id): ?UpdateInterface {
        return $this->updates[$id] ?? null;
    }
}

