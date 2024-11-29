<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update;

use Slothsoft\Devtools\Misc\Utils;

class Group {

    public string $id;

    public array $projects = [];

    public array $groups = [];

    public function __construct(string $id) {
        $this->id = Utils::normalize($id);
    }

    public function __toString(): string {
        return $this->id;
    }

    public function withProject(Project $project): Group {
        $this->projects[] = $project;
        return $this;
    }

    public function withGroup(Group $group): Group {
        $this->groups[] = $group;
        return $this;
    }

    public function run(string ...$updates) {
        $cwd = realpath(getcwd()) or die('missing cwd');

        foreach ($this->projects as $project) {
            echo "# $project";
            echo PHP_EOL;

            $_ENV = [];

            $time = new \DateTime();
            foreach ($updates as $update) {
                $update = $project->manager->getUpdate($update);
                printf('## %s', basename(get_class($update)));
                echo PHP_EOL;

                chdir($cwd);
                $update->runOn($project);

                echo PHP_EOL;
            }

            $delta = $time->diff(new \DateTime());
            printf('# %s: %s', $project, $delta->format('%i:%S'));
            echo PHP_EOL;
            echo PHP_EOL;
        }
        echo '...done!';
        echo PHP_EOL;

        chdir($cwd);
    }
}