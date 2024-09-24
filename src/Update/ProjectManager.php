<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update;

use Slothsoft\Devtools\Misc\Utils;
use Slothsoft\Devtools\Misc\Update\Analysis\AnalysisUpdateFactory;
use Slothsoft\Devtools\Misc\Update\Git\GitUpdateFactory;
use Slothsoft\Devtools\Misc\Update\Plastic\PlasticUpdateFactory;

class ProjectManager extends Group {

    public $workspaceDir;

    public array $updateFactories = [];

    public function __construct(string $id, string $workspaceDir, string $versionControl = '') {
        parent::__construct($id);
        $this->workspaceDir = realpath($workspaceDir) . DIRECTORY_SEPARATOR;

        $this->updateFactories[] = new AnalysisUpdateFactory();

        switch ($versionControl) {
            case 'git':
                $this->updateFactories[] = new GitUpdateFactory();
                break;
            case 'plastic':
                $this->updateFactories[] = new PlasticUpdateFactory();
                break;
        }
    }

    private array $updates = [];

    public function getUpdate(string $id): UpdateInterface {
        if (! isset($this->updates[$id])) {
            $update = $this->createUpdate($id);
            if ($update === null) {
                throw new \Exception("Failed to create Update for '$id'");
            }

            $this->updates[$id] = $update;
        }

        return $this->updates[$id];
    }

    public function createProject($project): Project {
        if (! is_array($project)) {
            $project = [
                'name' => $project
            ];
        }

        $this->loadProject($project);

        return new Project($this, $project);
    }

    protected function loadProject(array &$project): void {
        $project['workspaceId'] ??= Utils::toId($project['name']);
        $project['workspaceDir'] = $this->workspaceDir . $project['name'] . DIRECTORY_SEPARATOR;
        $project['gitignoreFile'] = $project['workspaceDir'] . '.gitignore';
        $project['buildpathFile'] = $project['workspaceDir'] . '.buildpath';
        $project['projectFile'] = $project['workspaceDir'] . '.project';
    }

    protected function createUpdate(string $id): ?UpdateInterface {
        foreach ($this->updateFactories as $factory) {
            $update = $factory->createUpdate($id);
            if ($update !== null) {
                return $update;
            }
        }
        return null;
    }

    public function addGroup(string $id, array $projects): void {
        $group = new Group("$this->id.$id");

        foreach ($projects as $project) {
            $group->projects[] = $this->createProject($project);
        }

        $this->groups[] = $group;
    }
}

