<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Unity;

use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;
use Slothsoft\Unity\UnityPackageInfo;
use Slothsoft\Unity\UnityProjectInfo;

class FixPackages implements UpdateInterface {

    private string $scope;

    private array $requiredDependencies = [];

    private array $optionalDependencies = [];

    private array $forbiddenDependencies = [];

    private array $forbiddenDependenciesForScope = [];

    public bool $alwaysSave = false;

    private array $info = [];

    public $homepageUrlDelegate;

    public $documentationUrlDelegate;

    public $changelogUrlDelegate;

    public $contributorsDelegate;

    public function __construct(string $scope) {
        $this->scope = $scope;
    }

    public function setRequiredDependencies(array $requiredDependencies): void {
        $this->requiredDependencies = $requiredDependencies;
    }

    public function setOptionalDependencies(array $optionalDependencies): void {
        $this->optionalDependencies = $optionalDependencies;
    }

    public function setForbiddenDependencies(array $forbiddenDependencies): void {
        $this->forbiddenDependencies = $forbiddenDependencies;
    }

    public function setAuthor(?string $author): void {
        $this->info['author'] = $author;
    }

    public function setUnity(?string $unity): void {
        $this->info['unity'] = $unity;
    }

    public function setUnityRelease(?string $release): void {
        $this->info['unityRelease'] = $release;
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

                        $manifest = &$package->package;

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

                        foreach ($this->optionalDependencies as $key => $val) {
                            if (isset($manifest['dependencies'][$key]) and $manifest['dependencies'][$key] !== $val) {
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

                        if (ReferenceSorter::sortPackages($manifest['dependencies'], true)) {
                            $hasChanged = true;
                        }

                        $info = $this->info;

                        $delegates = [
                            'homepage' => $this->homepageUrlDelegate,
                            'documentationUrl' => $this->documentationUrlDelegate,
                            'changelogUrl' => $this->changelogUrlDelegate,
                            'bugs' => $this->changelogUrlDelegate,
                            'contributors' => $this->contributorsDelegate
                        ];

                        foreach ($delegates as $key => $delegate) {
                            if ($delegate) {
                                $value = $delegate($project, $unity, $package);
                                if ($value !== null) {
                                    $info[$key] = $value;
                                }
                            }
                        }

                        foreach ($info as $key => $val) {
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

                        if (self::sortFields($manifest)) {
                            $hasChanged = true;
                        }

                        if ($hasChanged) {
                            $package->savePackage();
                        }
                    }
                }
            }
        }
    }

    private static function packageMatchesScope(string $packageName, string $scope): bool {
        return $scope === '' or stripos($packageName, $scope) === 0;
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

    private static array $fieldOrder = [
        "name" => null,
        "version" => null,
        "displayName" => null,
        "description" => "",
        "documentationUrl" => null,
        "homepage" => null,
        "changelogUrl" => null,
        "bugs" => null,
        "licensesUrl" => null,
        "unity" => null,
        "unityRelease" => null,
        "type" => "library",
        "hideInEditor" => false,
        "dependencies" => [],
        "keywords" => [],
        "author" => null,
        "contributors" => []
    ];

    private static function sortFields(array &$manifest) {
        foreach ($manifest as $key => $val) {
            if (! isset(self::$fieldOrder[$key])) {
                self::$fieldOrder[$key] = null;
            }
        }

        $new = [];
        foreach (self::$fieldOrder as $key => $val) {
            if (isset($manifest[$key])) {
                $val = $manifest[$key];
            }

            if ($val === '') {
                $val = null;
            }

            if ($val !== null) {
                $new[$key] = $val;
            }
        }

        if ($manifest === $new) {
            return false;
        }

        $manifest = $new;
        return true;
    }
}

