<?php
namespace Slothsoft\Devtools;

use Slothsoft\Devtools\Update\UpdateInterface;

abstract class ProjectManager
{
    private $workspaceDir;
    private $projects;
    
    public function __construct(string $workspaceDir, array $projects)
    {
        $this->workspaceDir = realpath($workspaceDir) . DIRECTORY_SEPARATOR;
        $this->projects = $projects;
        
        foreach ($this->projects as &$project) {
            $this->loadProject($project);
            $project['staticDir'] = $this->workspaceDir . 'devtools' . DIRECTORY_SEPARATOR . 'static' . DIRECTORY_SEPARATOR;
            $project['workspaceDir'] = $this->workspaceDir . $project['workspaceId'] . DIRECTORY_SEPARATOR;
            $project['assetsDir'] = $project['workspaceDir'] . 'assets' . DIRECTORY_SEPARATOR;
			$project['slothsoftDir'] = $project['workspaceDir'] . 'vendor' . DIRECTORY_SEPARATOR . 'slothsoft' . DIRECTORY_SEPARATOR;
            $project['composerFile'] = $project['workspaceDir'] . 'composer.json';
            $project['gitignoreFile'] = $project['workspaceDir'] . '.gitignore';
            $project['oldAssetsFile'] = $project['assetsDir'] . '.xml';
            $project['assetsFile'] = $project['assetsDir'] . 'manifest.xml';
            $project['buildpathFile'] = $project['workspaceDir'] . '.buildpath';
            $project['projectFile'] = $project['workspaceDir'] . '.project';
            $project['settingsFile'] = $project['workspaceDir'] . '.settings/org.eclipse.php.core.prefs';
            
            $project['sourceDir'] = $project['workspaceDir'] . 'src' . DIRECTORY_SEPARATOR;
            $project['docsDir'] = $project['workspaceDir'] . 'docs' . DIRECTORY_SEPARATOR;
            $project['testsDir'] = $project['workspaceDir'] . 'tests' . DIRECTORY_SEPARATOR;
        }
    }
    
    protected abstract function loadProject(array &$project);
    
    public function run(UpdateInterface... $updates) {
        if (count($updates) === 1) {
            foreach ($updates as $update) {
                printf('Running %s...%s', basename(get_class($update)), PHP_EOL);
                foreach ($this->projects as $project) {
                    echo $project['homeUrl'] .'...'. PHP_EOL;
                    chdir($project['workspaceDir']);
                    $update->runOn($project);
                }
            }
        } else {
            foreach ($this->projects as $project) {
                echo $project['homeUrl'] .'...'. PHP_EOL;
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

