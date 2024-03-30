<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc;

class Group {

    public string $id;

    public array $projects = [];

    public array $groups = [];

    public function __construct(string $id) {
        $this->id = CLI::normalize($id);
    }

    public function __toString(): string {
        return $this->id;
    }
}