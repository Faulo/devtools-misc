<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Unity;

use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;
use Slothsoft\Unity\UnityPackageInfo;
use Slothsoft\Unity\UnityProjectInfo;

class AddPackagesToProject implements UpdateInterface {

    private UnityProjectInfo $target;

    public function __construct(string $targetWorkspace) {
        $this->target = UnityProjectInfo::find($targetWorkspace, true);
    }

    public function runOn(Project $project) {
        if ($project->chdir()) {
            if ($unity = UnityProjectInfo::find('.', true)) {
                $manifest = &$this->target->manifest;
                $hasChanged = $this->alwaysSave;

                foreach (UnityPackageInfo::findAll("$unity->path/Packages") as $package) {
                    $id = $package->package['name'];
                    $version = $package->package['version'];

                    if (! isset($manifest['dependencies'][$id]) or $manifest['dependencies'][$id] !== $version) {
                        $manifest['dependencies'][$id] = $version;
                        $hasChanged = true;
                    }
                }

                if (ReferenceSorter::sortPackages($manifest['dependencies'])) {
                    $hasChanged = true;
                }

                if ($hasChanged) {
                    $this->target->saveManifest();
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

