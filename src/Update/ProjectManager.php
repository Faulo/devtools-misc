<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update;

class ProjectManager extends Group {

    protected $workspaceDir;

    public function __construct(string $id, string $workspaceDir) {
        parent::__construct($id);
        $this->workspaceDir = realpath($workspaceDir) . DIRECTORY_SEPARATOR;
    }

    private array $updates = [];

    public function getUpdate(string $id): UpdateInterface {
        if (! isset($this->updates[$id])) {
            $update = $this->createUpdate($id);
            if ($update === null) {
                throw new \Exception("Failed to create Update for '$id'");
            }

            $this->updates[$id] = $update;
        }

        return $this->updates[$id];
    }

    protected function createProject(array $info): Project {
        return new Project($this, $info);
    }

    protected function createUpdate($id): ?UpdateInterface {
        switch ($id) {
            case 'echo':
                return new Misc\EchoProject();
        }

        return null;
    }
}

