<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update;

use Slothsoft\Devtools\Misc\CLI;

class Project {

    public ProjectManager $manager;

    public string $name;

    public string $id;

    public string $workspace;

    public array $info;

    public function __construct(ProjectManager $manager, array $info) {
        $this->manager = $manager;
        $this->name = $info['name'];
        $this->id = $info['id'] ?? CLI::toId($this->name);
        $this->workspace = $info['workspaceDir'];
        $this->info = $info;
    }

    public function chdir(): bool {
        return is_dir($this->workspace) and chdir($this->workspace);
    }

    public function __toString(): string {
        return $this->name;
    }
}