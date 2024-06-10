<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Unity;

use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;
use Slothsoft\Unity\UnityPackageInfo;
use Slothsoft\Unity\UnityProjectInfo;

class FixChangelog implements UpdateInterface {

    const FILE_CHANGELOG = '/CHANGELOG.md';

    private string $scope;

    public bool $alwaysSave = false;

    private array $fixesForDependency = [];

    public function __construct(string $scope) {
        $this->scope = $scope;
    }

    public function setChangelogForDependency(string $dependency, string $changelog): void {
        $this->fixesForDependency[$dependency] = $changelog;
    }

    public function runOn(Project $project) {
        if ($project->chdir()) {
            if ($unity = UnityProjectInfo::find('.', true)) {
                foreach (UnityPackageInfo::findAll("$unity->path/Packages") as $package) {
                    $packageName = $package->getPackageName();
                    if (self::packageMatchesScope($packageName, $this->scope)) {
                        echo $packageName . PHP_EOL;

                        $manifest = $package->package;

                        $hasChanged = $this->alwaysSave;
                        $changelogPath = $package->path . self::FILE_CHANGELOG;
                        $changelog = file($changelogPath, FILE_IGNORE_NEW_LINES);

                        $i = array_search('## [Unreleased]', $changelog);
                        if ($i !== false) {
                            $version = explode('-', $manifest['version'])[0];
                            $date = date('Y-m-d');
                            $header = "## [$version] - $date";

                            if (! in_array($header, $changelog)) {
                                $changes = [];
                                $changes[] = '';
                                $changes[] = '';
                                $changes[] = $header;
                                $changes[] = '';

                                foreach ($this->fixesForDependency as $dependency => $change) {

                                    if (isset($manifest['dependencies'][$dependency])) {
                                        $changes[] = $change;
                                        $hasChanged = true;
                                    }
                                }

                                array_splice($changelog, $i + 1, 0, $changes);

                                if ($hasChanged) {
                                    $changelog = implode(PHP_EOL, $changelog);
                                    file_put_contents($changelogPath, $changelog);
                                }
                            }
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

