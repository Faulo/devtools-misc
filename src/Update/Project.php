<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update;

use Slothsoft\Devtools\Misc\CLI;

class Project {

    public ProjectManager $manager;

    public string $name;

    public string $id;

    public array $info;

    public function __construct(ProjectManager $manager, array $info) {
        $this->manager = $manager;
        $this->name = $info['name'];
        $this->id = CLI::toId($info['name']);
        $this->info = $info;
    }

    public function __toString(): string {
        return $this->name;
    }
}