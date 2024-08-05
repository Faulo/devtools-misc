<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update;

use Slothsoft\Devtools\Misc\Utils;

class Project {

    public ProjectManager $manager;

    public string $name;

    public string $id;

    public string $workspace;

    public ?string $repository;

    public array $info;

    public function __construct(ProjectManager $manager, array $info) {
        $this->manager = $manager;
        $this->name = $info['name'];
        $this->id = $info['id'] ?? Utils::toId($this->name);
        $this->workspace = is_dir($info['workspaceDir']) ? realpath($info['workspaceDir']) : $info['workspaceDir'];
        $this->repository = $info['repository'] ?? null;
        $this->info = $info;
    }

    public function chdir(): bool {
        clearstatcache();
        return is_dir($this->workspace) and chdir($this->workspace);
    }

    public function __toString(): string {
        return $this->name;
    }
}