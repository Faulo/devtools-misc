<?php
namespace Slothsoft\Devtools\Composer;

use Composer\Json\JsonFile;

class ComposerManifest {

    private $file;

    private $data = [];

    public function __construct(string $path) {
        $this->file = new JsonFile($path);
    }

    public function load() {
        $this->data = $this->file->read();
    }

    public function save() {
        $this->file->write($this->data);
    }

    public function getData(): array {
        return $this->data;
    }

    public function setName(string $name) {
        $this->data['name'] = $name;
    }

    public function getVersion() {
        return $this->data['version'] ?? null;
    }

    public function setVersion(string $version) {
        if ($version === '') {
            unset($this->data['version']);
        } else {
            $this->data['version'] = $version;
        }
    }

    public function setLicense(string $license) {
        if ($license === '') {
            unset($this->data['license']);
        } else {
            $this->data['license'] = $license;
        }
    }

    public function setHomepage(string $url) {
        if ($url === '') {
            unset($this->data['homepage']);
        } else {
            $this->data['homepage'] = $url;
        }
    }

    public function setAuthor(string $name, string $email) {
        $this->data["authors"] = [];
        $this->data["authors"][] = [
            'name' => $name,
            'email' => $email
        ];
    }

    public function setKeywords(array $list) {
        $this->data['keywords'] = $list;
    }

    public function getKeywords(): array {
        return $this->data['keywords'] ?? [];
    }

    public function setRequireProd(array $list) {
        $this->data['require'] = $list;
    }

    public function getRequireProd(): array {
        return $this->data['require'] ?? [];
    }

    public function setRequireDev(array $list) {
        $this->data['require-dev'] = $list;
    }

    public function getRequireDev(): array {
        return $this->data['require-dev'] ?? [];
    }

    public function setAutoloadingProd(array $list) {
        $this->data['autoload'] = $list;
    }

    public function getAutoloadingProd(): array {
        return $this->data['autoload'] ?? [];
    }

    public function getAutoloadingProdPsr4(): array {
        return $this->getAutoloadingProd()['psr-4'] ?? [];
    }

    public function getAutoloadingProdPsr0(): array {
        return $this->getAutoloadingProd()['psr-0'] ?? [];
    }

    public function setAutoloadingDev(array $list) {
        $this->data['autoload-dev'] = $list;
    }

    public function setOptimizeAutoloader(bool $optimize) {
        $this->data['classmap-authoritative'] = $optimize;
        unset($this->data['optimize-autoloader']);
        unset($this->data['apcu-autoloader']);
    }

    public function setAllowDevMaster(bool $allow) {
        if ($allow) {
            $this->data['minimum-stability'] = 'dev';
            $this->data['prefer-stable'] = true;
        } else {
            unset($this->data['minimum-stability']);
            unset($this->data['prefer-stable']);
        }
    }

    public function getRelatedProjects(): array {
        $ret = [];
        foreach ($this->getRequireProd() as $module => $version) {
            if (strpos($module, 'slothsoft/') === 0) {
                $ret[] = '/' . str_replace('/', '-', $module);
            }
        }
        return $ret;
    }

    public function setScripts(array $list) {
        $this->data['scripts'] = $list;
        if (! count($list)) {
            unset($this->data['scripts']);
        }
    }
}

