<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc;

class Project {

    public string $name;

    public string $id;

    public array $info;

    public function __construct(string $name, array $info) {
        $this->name = $name;
        $this->id = CLI::toId($name);
        $this->info = $info;
    }

    public function __toString(): string {
        return $this->name;
    }
}