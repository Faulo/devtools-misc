<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Unity;

use Slothsoft\Devtools\Misc\Utils;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;
use Slothsoft\Unity\UnityProjectInfo;

class FixPackages implements UpdateInterface {

    private array $forbiddenDependencies = [];

    private array $requiredDependencies = [];

    private array $scopedRegistries = [];

    private bool $alwaysSave = false;

    public function __construct(array $scopedRegistries, array $requiredDependencies, array $forbiddenDependencies) {
        $this->scopedRegistries = $scopedRegistries;
        $this->requiredDependencies = $requiredDependencies;
        $this->forbiddenDependencies = $forbiddenDependencies;
    }

    public function runOn(Project $project) {
        if ($project->chdir()) {
            if ($unity = UnityProjectInfo::find('.', true)) {
                if ($manifestPath = realpath($unity->path . '/Packages/manifest.json')) {
                    $manifest = Utils::readJson($manifestPath);

                    $hasChanged = $this->alwaysSave;

                    if (! isset($manifest['dependencies'])) {
                        $manifest['dependencies'] = [];
                        $hasChanged = true;
                    }

                    foreach ($this->forbiddenDependencies as $key) {
                        if (isset($manifest['dependencies'][$key])) {
                            unset($manifest['dependencies'][$key]);
                            $hasChanged = false;
                        }
                    }

                    foreach ($this->requiredDependencies as $key => $val) {
                        if (isset($manifest['dependencies'][$key]) and $manifest['dependencies'][$key] !== $val) {
                            $manifest['dependencies'][$key] = $val;
                            $hasChanged = false;
                        }
                    }

                    if ($manifest['scopedRegistries'] !== $this->scopedRegistries) {
                        $manifest['scopedRegistries'] = $this->scopedRegistries;
                        $hasChanged = true;
                    }

                    $sortedDependencies = $manifest['dependencies'];
                    ksort($sortedDependencies);
                    $dependencies = [];
                    $modules = [];
                    foreach ($sortedDependencies as $key => $val) {
                        if (strpos($key, 'com.unity.modules.') === 0) {
                            $modules[$key] = $val;
                        } else {
                            $dependencies[$key] = $val;
                        }
                    }
                    $sortedDependencies = $dependencies + $modules;

                    if ($manifest['dependencies'] !== $sortedDependencies) {
                        $manifest['dependencies'] = $sortedDependencies;
                        $hasChanged = true;
                    }

                    if ($hasChanged) {
                        Utils::writeJson($manifestPath, $manifest, 2, "\n");
                    }
                }
            }
        }
    }
}

