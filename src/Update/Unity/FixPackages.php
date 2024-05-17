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

    private array $forbiddenDependenciesForScope = [];

    private array $requiredDependencies;

    private bool $alwaysSave;

    private array $info = [];

    public function __construct(string $scope, array $requiredDependencies = [], array $forbiddenDependencies = [], bool $alwaysSave = false) {
        $this->scope = $scope;
        $this->requiredDependencies = $requiredDependencies;
        $this->forbiddenDependencies = $forbiddenDependencies;
        $this->alwaysSave = $alwaysSave;
    }

    public function setAuthor(?string $author): void {
        $this->info['author'] = $author;
    }

    public function setUnity(?string $unity): void {
        $this->info['unity'] = $unity;
    }

    public function addForbiddenDependencyForScope(string $scope, array $forbiddenDependencies): void {
        $this->forbiddenDependenciesForScope[$scope] = $forbiddenDependencies;
    }

    public function runOn(Project $project) {
        if ($project->chdir()) {
            if ($unity = UnityProjectInfo::find('.', true)) {
                foreach (UnityPackageInfo::findAll("$unity->path/Packages") as $package) {
                    $packageName = $package->getPackageName();
                    if (self::packageMatchesScope($packageName, $this->scope)) {
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

                        foreach ($this->getForbiddenDependencies($packageName) as $key) {
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

                        foreach ($this->info as $key => $val) {
                            if ($val) {
                                if (! isset($manifest[$key]) or $manifest[$key] !== $val) {
                                    $manifest[$key] = $val;
                                    $hasChanged = true;
                                }
                            } else {
                                if (isset($manifest[$key])) {
                                    unset($manifest[$key]);
                                    $hasChanged = true;
                                }
                            }
                        }
                        if ($hasChanged) {
                            Utils::writeJson($manifestPath, $manifest, 2);
                        }
                    }
                }
            }
        }
    }

    private static function packageMatchesScope(string $packageName, string $scope): bool {
        return strpos($packageName, $scope) === 0;
    }

    private function getForbiddenDependencies(string $packageName): iterable {
        yield from $this->forbiddenDependencies;

        foreach ($this->forbiddenDependenciesForScope as $scope => $dependencies) {
            if (self::packageMatchesScope($packageName, $scope)) {
                yield from $dependencies;
            }
        }

        yield $packageName;
    }
}

