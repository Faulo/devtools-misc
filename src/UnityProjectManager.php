<?php
namespace Slothsoft\Devtools\Misc;

class UnityProjectManager extends ProjectManager {

    public function addGroup(string $id, array $projects): void {
        $group = new Group("$this->id.$id");

        foreach ($projects as $name) {
            $project = [];
            $project['workspaceId'] = CLI::toId($name);
            $project['workspaceDir'] = $this->workspaceDir . $name . DIRECTORY_SEPARATOR;
            $project['gitignoreFile'] = $project['workspaceDir'] . '.gitignore';
            $project['buildpathFile'] = $project['workspaceDir'] . '.buildpath';
            $project['projectFile'] = $project['workspaceDir'] . '.project';

            $group->projects[] = new Project($name, $project);
        }

        $this->groups[] = $group;
    }
}

