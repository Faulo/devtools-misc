<?php
namespace Slothsoft\Devtools\Misc;

use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class ProjectManager extends Group {

    protected $workspaceDir;

    public array $groups = [];

    public function __construct(string $id, string $workspaceDir) {
        parent::__construct($id);
        $this->workspaceDir = realpath($workspaceDir) . DIRECTORY_SEPARATOR;
    }

    public function run(UpdateInterface ...$updates) {
        if (count($updates) === 1) {
            foreach ($updates as $update) {
                printf('Running %s...%s', basename(get_class($update)), PHP_EOL);
                foreach ($this->projects as $project) {
                    echo $project['homeUrl'] . '...' . PHP_EOL;
                    chdir($project['workspaceDir']);
                    $update->runOn($project);
                }
            }
        } else {
            foreach ($this->projects as $project) {
                echo $project['homeUrl'] . '...' . PHP_EOL;
                foreach ($updates as $update) {
                    printf('Running %s...%s', basename(get_class($update)), PHP_EOL);
                    chdir($project['workspaceDir']);
                    $update->runOn($project);
                }
            }
        }
        printf('...done!');
    }
}

