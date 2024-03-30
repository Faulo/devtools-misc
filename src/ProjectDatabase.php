<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc;

class ProjectDatabase {

    private static $_instance;

    public static function instance(): ProjectDatabase {
        if (self::$_instance === null) {
            self::$_instance = new Projectdatabase();
        }

        return self::$_instance;
    }

    private array $projects = [];

    private function getAllProjects(): iterable {
        $set = [];
        foreach ($this->projects as $project) {
            if (! in_array($project, $set, true)) {
                $set[] = $project;
                yield $project;
            }
        }

        foreach ($this->getAllGroups() as $group) {
            foreach ($group->projects as $project) {
                if (! in_array($project, $set, true)) {
                    $set[] = $project;
                    yield $project;
                }
            }
        }
    }

    public function registerProject(Project $project): void {
        $this->projects[] = $project;
    }

    private array $groups = [];

    private function getAllGroups(): iterable {
        $set = [];
        yield from self::unroll($this->groups, $set);
    }

    static function unroll(array $groups, array &$set): iterable {
        foreach ($groups as $group) {
            if (! in_array($group, $set, true)) {
                $set[] = $group;
                yield $group;
                yield from self::unroll($group->groups, $set);
            }
        }
    }

    public function registerGroup(Group $group): void {
        $this->groups[] = $group;
    }

    public function getProjects(string ...$ids): iterable {
        $set = [];

        foreach ($this->getAllProjects() as $project) {
            if (in_array($project->id, $ids, true)) {
                $set[] = $project;
            }
        }

        foreach ($this->getAllGroups() as $group) {
            if (in_array($group->id, $ids, true)) {
                $s = [];
                foreach (self::unroll([
                    $group
                ], $s) as $g) {
                    $set = array_merge($set, $g->projects);
                }
            }
        }

        return array_unique($set);
    }
}