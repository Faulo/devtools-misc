<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update;

class ProjectDatabase extends Group {

    private static $_instance;

    public static function instance(): ProjectDatabase {
        if (self::$_instance === null) {
            self::$_instance = new Projectdatabase('_');
        }

        return self::$_instance;
    }

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

    private function getAllGroups(): iterable {
        $set = [];
        yield from self::unroll($this, $set);
    }

    private static function unroll(Group $group, array &$set): iterable {
        if (! in_array($group, $set, true)) {
            $set[] = $group;
            yield $group;
            foreach ($group->groups as $g) {
                yield from self::unroll($g, $set);
            }
        }
    }

    public function getProjects(string ...$ids): Group {
        $set = [];

        foreach ($this->getAllProjects() as $project) {
            if (in_array($project->id, $ids, true)) {
                $set[] = $project;
            }
        }

        foreach ($this->getAllGroups() as $group) {
            if (in_array($group->id, $ids, true)) {
                $s = [];
                foreach (self::unroll($group, $s) as $g) {
                    $set = array_merge($set, $g->projects);
                }
            }
        }

        $group = new Group(implode('|', $ids));
        $group->projects = array_unique($set);

        return $group;
    }
}