<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update;

abstract class PHPProjectManager extends ProjectManager {

    public function __construct(string $id, string $workspaceDir, array $projects) {
        parent::__construct($id, $workspaceDir);

        foreach ($projects as &$project) {
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

            $project['composer'] = null;
            $project['namespace'] = null;
            if (is_file($project['composerFile'])) {
                $project['composer'] = json_decode(file_get_contents($project['composerFile']), true);
                if (isset($project['composer']['autoload']['psr-4'])) {
                    $project['namespace'] = array_keys($project['composer']['autoload']['psr-4'])[0];
                }
            }

            $this->projects[] = $this->createProject($project);
        }
    }

    protected abstract function loadProject(array &$project);

    protected function createUpdate(string $id): ?UpdateInterface {
        switch ($id) {
            case 'update':
                return new Composer\UpdateUsingCLI();
        }

        return parent::createUpdate($id);
    }
}

