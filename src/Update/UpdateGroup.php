<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update;

class UpdateGroup implements UpdateInterface {

    public array $updates;

    public function __construct(UpdateInterface ...$updates) {
        $this->updates = $updates;
    }

    public function runOn(Project $project) {
        foreach ($this->updates as $update) {
            $update->runOn($project);
        }
    }
}
