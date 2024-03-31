<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update;

use Slothsoft\Devtools\Misc\CLI;

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

    public function run(string ...$updates) {
        foreach ($this->projects as $project) {
            echo "# $project" . PHP_EOL;
            foreach ($updates as $update) {
                $update = $project->manager->getUpdate($update);
                printf('Running %s...%s', basename(get_class($update)), PHP_EOL);
                $update->runOn($project);
            }
        }
        printf('...done!');
    }
}