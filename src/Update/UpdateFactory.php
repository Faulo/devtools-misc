<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update;

class UpdateFactory {

    public array $updates = [];

    public function addUpdate(string $id, UpdateInterface $update): void {
        $this->updates[$id] = $update;
    }

    public function createUpdate(string $id): ?UpdateInterface {
        return $this->updates[$id] ?? null;
    }
}

