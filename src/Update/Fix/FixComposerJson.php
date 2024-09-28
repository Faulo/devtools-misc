<?php
namespace Slothsoft\Devtools\Misc\Update\Fix;

use Slothsoft\Core\FileSystem;
use Slothsoft\Devtools\Misc\Composer\ComposerManifest;
use Slothsoft\Devtools\Misc\Update\PHPExecutor;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class FixComposerJson implements UpdateInterface {

    private array $project = [];

    private PHPExecutor $php;

    private ComposerManifest $composer;

    private array $prodModules = [];

    private array $devModules = [];

    private function getAutoloadFiles() {
        return [
            'config.php',
            'src/autoload.php',
            'src/registerModule.php'
        ];
    }

    private function getDeprecatedModules(): array {
        return [
            'slothsoft/dbms',
            'slothsoft/pt',
            'slothsoft/extensions',
            'slothsoft/'
        ];
    }

    private function getDefaultProdModules(): array {
        return [
            'php' => '^7.2'
        ];
    }

    private function getDefaultDevModules(): array {
        return [
            'phpunit/phpunit' => '^6.5'
        ];
    }

    private function getDefaultLicence(): string {
        return '';
    }

    public function runOn(Project $project) {
        if (! $project->chdir()) {
            return;
        }

        $this->project = $project->info;
        $this->php = new PHPExecutor();

        $this->composer = new ComposerManifest($this->project['composerFile']);

        $this->composer->load();

        $this->composer->setName($this->project['composerId']);

        // $this->composer->setVersion('');

        // $this->composer->setLicense('WTFPL');

        // $this->composer->setAuthor('Daniel Schulz', 'info.slothsoft@gmail.com');

        if ($this->isServer()) {
            $this->composer->setHomepage($this->project['homeUrl']);
        } else {
            $this->composer->setHomepage('http://farah.slothsoft.net/modules/' . $this->project['name']);
        }

        // $this->composer->setKeywords($this->getKeywords());

        // $this->composer->setRequireProd($this->getProdModules());

        // $this->composer->setRequireDev($this->getDevModules());

        // $this->composer->setAutoloadingProd($this->getProdAutoloading());

        // $this->composer->setAutoloadingDev($this->getDevAutoloading());

        $this->composer->clearOptimizeAutoloader();

        // $this->composer->setOptimizeAutoloader($this->isServer());

        $this->composer->setAllowDevMaster(false);

        $this->composer->setScripts($this->getScripts());

        if ($version = $this->composer->data['extra']['branch-alias']['dev-develop'] ?? null) {
            $this->composer->data['extra']['branch-alias'] = [
                'dev-main' => $version
            ];
        }

        unset($this->composer->data['config']);

        if (isset($_ENV['PHP_VERSION'])) {
            // $this->composer->data['require']['php'] = '>=' . $_ENV['PHP_VERSION'];
        }

        if ($this->isServer()) {
            unset($this->composer->data['require']['php']);

            if (! is_dir('src')) {
                mkdir('src', 0777);
            }

            $target = 'src/bootstrap.php';
            $source = $target;
            foreach ([
                'bootstrap.php',
                'registerModule.php'
            ] as $file) {
                foreach ([
                    'scripts',
                    'src'
                ] as $dir) {
                    if (is_file("$dir/$file")) {
                        $source = "$dir/$file";
                        break 2;
                    }
                }
            }
            if ($source !== $target) {
                rename($source, $target);
            }

            $this->composer->data['autoload']['files'] = [
                $target
            ];
        }

        $this->composer->save();

        // $this->php->composer('require', "php=^{$this->php->version}");

        if ($this->isServer()) {
            $this->php->composer('require', 'slothsoft/farah', 'slothsoft/core');
        }
    }

    private function getProdModules() {
        $this->prodModules = [];
        $this->addProdModules($this->composer->getRequireProd());
        $this->addProdModules($this->getDefaultProdModules());

        $this->removeProdModule(...$this->getDeprecatedModules());
        $this->removeProdModule($this->project['composerId']);

        ksort($this->prodModules);

        return $this->prodModules;
    }

    private function getKeywords(): array {
        // $ret = $this->composer->getKeywords();
        $ret = [];
        $ret[] = 'slothsoft';
        if (is_file($this->project['assetsFile'])) {
            $ret[] = 'farah-module';
        }
        if (is_file($this->project['workspaceDir'] . 'config.php')) {
            $ret[] = 'farah-server';
        }
        if (count(FileSystem::scanDir($this->project['workspaceDir'] . 'src', FileSystem::SCANDIR_EXCLUDE_FILES))) {
            $ret[] = 'docs';
        }
        // $ret = array_unique($ret);
        // sort($ret);
        return $ret;
    }

    private function isServer(): bool {
        return in_array('farah-server', $this->composer->getKeywords());
    }

    private function addProdModules(array $moduleList) {
        foreach ($moduleList as $module => $version) {
            if ($this->isSlothsoftModule($module)) {
                $this->prodModules[$module] = $this->isServer() ? '*' : $version;
            } else {
                $this->prodModules[$module] = $version;
            }
        }
    }

    private function removeProdModule(string ...$moduleList) {
        foreach ($moduleList as $module) {
            if (isset($this->prodModules[$module])) {
                unset($this->prodModules[$module]);
            }
        }
    }

    private function getDevModules(): array {
        $this->devModules = [];
        $this->addDevModules($this->composer->getRequireDev());
        if (is_dir($this->project['slothsoftDir'])) {
            foreach (array_diff(scandir($this->project['slothsoftDir']), [
                '.',
                '..'
            ]) as $module) {
                $this->addDevModules([
                    "slothsoft/$module" => '*'
                ]);
            }
        }
        $this->addDevModules($this->getDefaultDevModules());

        $this->removeDevModule(...$this->getDeprecatedModules());
        $this->removeDevModule($this->project['composerId']);

        ksort($this->devModules);

        return $this->devModules;
    }

    private function addDevModules(array $moduleList) {
        foreach ($moduleList as $module => $version) {
            if ($this->isSlothsoftModule($module)) {
                // $this->devModules[$module] = 'dev-master';
            } else {
                $this->devModules[$module] = $version;
            }
        }
    }

    private function removeDevModule(string ...$moduleList) {
        foreach ($moduleList as $module) {
            if (isset($this->devModules[$module])) {
                unset($this->devModules[$module]);
            }
        }
    }

    private function isSlothsoftModule(string $module): bool {
        return explode('/', $module)[0] === 'slothsoft';
    }

    private function getProdAutoloading(): array {
        $ret = [];

        $autoloading = [];
        $autoloading += $this->composer->getAutoloadingProdPsr4();
        $autoloading += $this->composer->getAutoloadingProdPsr0();
        foreach ($autoloading as &$val) {
            $val = 'src/';
        }
        unset($val);

        if (count($autoloading)) {
            $ret['psr-0'] = $autoloading;
        }

        $files = [];
        foreach ($this->getAutoloadFiles() as $path) {
            if (is_file($this->project['workspaceDir'] . $path)) {
                $files[] = $path;
            }
        }
        if (count($files)) {
            $ret['files'] = $files;
        }

        return $ret;
    }

    private function getDevAutoloading(): array {
        return [
            'classmap' => [
                'tests/'
            ]
        ];
    }

    private function getScripts(): array {
        if ($this->isServer()) {
            return [
                // 'post-autoload-dump' => 'composer exec server-clean cache',
                'farah-asset' => '@php vendor/slothsoft/farah/scripts/farah-asset.php',
                'farah-page' => '@php vendor/slothsoft/farah/scripts/farah-page.php'
            ];
        } else {
            return [];
        }
    }
}

