<?php
namespace Slothsoft\Devtools\Update\Fix;

use Slothsoft\Devtools\Composer\ComposerManifest;
use Slothsoft\Devtools\Update\UpdateInterface;
use Slothsoft\Core\FileSystem;

class FixComposerJson implements UpdateInterface
{
    private $project;
    private $composer;
    private $prodModules;
    private $devModules;
    
    private function getAutoloadFiles() {
        return [
            'config.php',
            'src/autoload.php',
            'src/registerModule.php',
        ];
    }
    private function getDeprecatedModules() : array {
        return [
            'slothsoft/dbms',
            'slothsoft/pt',
            'slothsoft/extensions',
            'slothsoft/',
        ];
    }
    private function getDefaultProdModules() : array {
        return [
            'php' => '^7.2',
        ];
    }
    private function getDefaultDevModules() : array {
        return [
            'phpunit/phpunit' => '^6.5',
        ];
    }
    private function getDefaultLicence() : string {
        return '';
    }
    
    public function runOn(array $project)
    {
        $this->project = $project;
        $this->composer = new ComposerManifest($this->project['composerFile']);
        
        $this->composer->load();
        
        $this->composer->setName($this->project['composerId']);
        
        if ($this->isServer()) {
            $this->composer->setVersion('');
        } else {
            if (!$this->composer->getVersion()) {
                $this->composer->setVersion('1.0.0');
            }
        }
        
        $this->composer->setKeywords($this->getKeywords());
        
        $this->composer->setRequireProd($this->getProdModules());
        
        $this->composer->setRequireDev($this->getDevModules());
        
        $this->composer->setAutoloadingProd($this->getProdAutoloading());
        
        $this->composer->setAutoloadingDev($this->getDevAutoloading());
        
        $this->composer->setOptimizeAutoloader($this->isServer());
        
        $this->composer->setAllowDevMaster(false);
        
        $this->composer->setScripts($this->getScripts());
        
        $this->composer->save();
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
    private function getKeywords() : array {
        //$ret = $this->composer->getKeywords();
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
        //$ret = array_unique($ret);
        //sort($ret);
        return $ret;
    }
    private function isServer() : bool {
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
    private function removeProdModule(string... $moduleList) {
        foreach ($moduleList as $module) {
            if (isset($this->prodModules[$module])) {
                unset($this->prodModules[$module]);
            }
        }
    }
    
    
    private function getDevModules() : array {
        $this->devModules = [];
        $this->addDevModules($this->composer->getRequireDev());
        if (is_dir($this->project['slothsoftDir'])) {
            foreach (array_diff(scandir($this->project['slothsoftDir']), ['.', '..']) as $module) {
                $this->addDevModules(["slothsoft/$module" => '*']);
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
                //$this->devModules[$module] = 'dev-master';
            } else {
                $this->devModules[$module] = $version;
            }
        }
    }
    private function removeDevModule(string... $moduleList) {
        foreach ($moduleList as $module) {
            if (isset($this->devModules[$module])) {
                unset($this->devModules[$module]);
            }
        }
    }
    private function isSlothsoftModule(string $module) : bool {
        return explode('/', $module)[0] === 'slothsoft';
    }
    
    private function getProdAutoloading() : array {
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
    
    private function getDevAutoloading() : array {
        return [
            'classmap' => [
                'tests/',
            ]
        ];
    }
    
    private function getScripts() : array {
        if ($this->isServer()) {
            return ['post-autoload-dump' => 'Slothsoft\\Core\\ServerEnvironment::cleanCacheDirectory'];
        } else {
            return [];
        }
    }
}

