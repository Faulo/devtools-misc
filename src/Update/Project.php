<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update;

use Dotenv\Dotenv;
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
        $this->workspace = $info['workspaceDir'] ?? $manager->workspaceDir . $this->id;
        if (is_dir($this->workspace)) {
            $this->workspace = realpath($this->workspace);
        }
        $this->repository = $info['repository'] ?? null;
        $this->info = $info;
    }

    public function chdir(): bool {
        clearstatcache();
        if (is_dir($this->workspace) and chdir($this->workspace)) {
            $_ENV = [];
            if (is_file('.env')) {
                $dotenv = Dotenv::createMutable($this->workspace);
                $dotenv->load();
            }

            return true;
        }

        return false;
    }

    public function __toString(): string {
        return $this->name;
    }
}