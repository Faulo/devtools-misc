<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update;

use Slothsoft\Devtools\Misc\CLI;

class UnityProjectManager extends ProjectManager {

    public function addGroup(string $id, array $projects): void {
        $group = new Group("$this->id.$id");

        foreach ($projects as $name) {
            $project = [];
            $project['name'] = $name;
            $project['workspaceId'] = CLI::toId($name);
            $project['workspaceDir'] = $this->workspaceDir . $name . DIRECTORY_SEPARATOR;
            $project['gitignoreFile'] = $project['workspaceDir'] . '.gitignore';
            $project['buildpathFile'] = $project['workspaceDir'] . '.buildpath';
            $project['projectFile'] = $project['workspaceDir'] . '.project';

            $group->projects[] = $this->createProject($project);
        }

        $this->groups[] = $group;
    }
}

