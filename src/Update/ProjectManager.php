<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update;

class ProjectManager extends Group {

    public string $vc = '';

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

    protected function createUpdate(string $id): ?UpdateInterface {
        switch ($id) {
            case 'echo':
                return new Analysis\EchoProject();
            case 'pull':
                switch ($this->vc) {
                    case 'git':
                        return new Git\Pull();
                    case 'plastic':
                        return new Plastic\Pull();
                }
            case 'reset':
                switch ($this->vc) {
                    case 'git':
                        return new Git\Reset();
                    case 'plastic':
                        return new Plastic\Reset();
                }
        }

        return null;
    }
}

