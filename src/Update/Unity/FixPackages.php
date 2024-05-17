<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Unity;

use Slothsoft\Devtools\Misc\Utils;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;
use Slothsoft\Unity\UnityPackageInfo;
use Slothsoft\Unity\UnityProjectInfo;

class FixPackages implements UpdateInterface {

    private string $scope;

    private array $forbiddenDependencies;

    private array $requiredDependencies;

    private bool $alwaysSave;

    public function __construct(string $scope, array $requiredDependencies = [], array $forbiddenDependencies = [], bool $alwaysSave = false) {
        $this->scope = $scope;
        $this->requiredDependencies = $requiredDependencies;
        $this->forbiddenDependencies = $forbiddenDependencies;
        $this->alwaysSave = $alwaysSave;
    }

    public function runOn(Project $project) {
        if ($project->chdir()) {
            if ($unity = UnityProjectInfo::find('.', true)) {
                foreach (UnityPackageInfo::findAll("$unity->path/Packages") as $package) {
                    $packageName = $package->getPackageName();
                    if (strpos($packageName, $this->scope) === 0) {
                        echo $packageName . PHP_EOL;

                        $manifest = $package->package;
                        $manifestPath = $package->path . UnityPackageInfo::FILE_PACKAGE;

                        $hasChanged = $this->alwaysSave;

                        if (! isset($manifest['dependencies'])) {
                            $manifest['dependencies'] = [];
                            $hasChanged = true;
                        }
                        foreach ($this->requiredDependencies as $key => $val) {
                            if (! isset($manifest['dependencies'][$key]) or $manifest['dependencies'][$key] !== $val) {
                                $manifest['dependencies'][$key] = $val;
                                $hasChanged = true;
                            }
                        }

                        foreach ([
                            ...$this->forbiddenDependencies,
                            $packageName
                        ] as $key) {
                            if (isset($manifest['dependencies'][$key])) {
                                unset($manifest['dependencies'][$key]);
                                $hasChanged = true;
                            }
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
                            Utils::writeJson($manifestPath, $manifest, 2);
                        }
                    }
                }
            }
        }
    }
}

