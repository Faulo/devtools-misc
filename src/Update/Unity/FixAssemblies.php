<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Unity;

use Slothsoft\Devtools\Misc\Utils;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;
use Slothsoft\Unity\UnityPackageInfo;
use Slothsoft\Unity\UnityProjectInfo;

class FixAssemblies implements UpdateInterface {

    private string $scope;

    public bool $alwaysSave = false;

    public bool $logMissingEditorTests = true;

    public function __construct(string $scope) {
        $this->scope = $scope;
    }

    private $defineConstraints = [
        'UNITY_INCLUDE_TESTS'
    ];

    public function addDefineConstraint(string $constraint) {
        $this->defineConstraints[] = $constraint;
    }

    private $precompiledReferences = [
        'nunit.framework.dll'
    ];

    public function addPrecompiledReference(string $reference) {
        $this->precompiledReferences[] = $reference;
    }

    private $editorTestReferences = [
        "UnityEngine.TestRunner",
        "UnityEditor.TestRunner"
    ];

    public function addEditorTestReference(string $reference) {
        $this->editorTestReferences[] = $reference;
    }

    private $editorTestClasses = [];

    public function addEditorTestClass(string $path, string $code) {
        $this->editorTestClasses[$path] = $code;
    }

    public function runOn(Project $project) {
        if ($project->chdir()) {
            if ($unity = UnityProjectInfo::find('.', true)) {
                foreach (UnityPackageInfo::findAll("$unity->path/Packages") as $package) {
                    $packageName = $package->getPackageName();
                    if (self::packageMatchesScope($packageName, $this->scope)) {
                        echo $packageName . PHP_EOL;

                        chdir($package->path);

                        $hasEditorTests = false;

                        foreach (glob('*/*.asmdef') as $assemblyPath) {
                            if ($assembly = Utils::readJson($assemblyPath)) {
                                $previous = $assembly;
                                $hasChanged = $this->alwaysSave;

                                if (ReferenceSorter::sortAssemblies($assembly['references'])) {
                                    $hasChanged = true;
                                }

                                $assembly['autoReferenced'] = true;

                                if ($assembly !== $previous) {
                                    $hasChanged = true;
                                }

                                if ($hasChanged) {
                                    Utils::writeJson($assemblyPath, $assembly, 4);
                                }
                            }
                        }

                        foreach (glob('Tests/*/*.asmdef') as $assemblyPath) {
                            if ($assembly = Utils::readJson($assemblyPath)) {
                                $previous = $assembly;
                                $hasChanged = $this->alwaysSave;

                                $isEditorTests = strpos($assemblyPath, '.Tests.Editor.');

                                if ($isEditorTests) {
                                    $hasEditorTests = true;

                                    $assembly['includePlatforms'] = [
                                        "Editor"
                                    ];

                                    foreach ($this->editorTestReferences as $reference) {
                                        if (! in_array($reference, $assembly['references'])) {
                                            $assembly['references'][] = $reference;
                                            $hasChanged = true;
                                        }
                                    }

                                    foreach ($this->editorTestClasses as $path => $code) {
                                        $testsFile = dirname($assemblyPath) . DIRECTORY_SEPARATOR . $path;
                                        $testsClass = sprintf($code, $assembly['name']);
                                        file_put_contents($testsFile, $testsClass);
                                    }
                                } else {
                                    $assembly['includePlatforms'] = [];
                                }

                                if (ReferenceSorter::sortAssemblies($assembly['references'])) {
                                    $hasChanged = true;
                                }

                                $assembly['excludePlatforms'] = [];
                                $assembly['overrideReferences'] = true;

                                foreach ($this->precompiledReferences as $reference) {
                                    if (! in_array($reference, $assembly['precompiledReferences'])) {
                                        $assembly['precompiledReferences'][] = $reference;
                                        $hasChanged = true;
                                    }
                                }
                                sort($assembly['precompiledReferences']);

                                $assembly['autoReferenced'] = false;

                                foreach ($this->defineConstraints as $constraint) {
                                    if (! in_array($constraint, $assembly['defineConstraints'])) {
                                        $assembly['defineConstraints'][] = $constraint;
                                        $hasChanged = true;
                                    }
                                }
                                sort($assembly['defineConstraints']);

                                if ($assembly !== $previous) {
                                    $hasChanged = true;
                                }

                                if ($hasChanged) {
                                    Utils::writeJson($assemblyPath, $assembly, 4);
                                }
                            }
                        }

                        if (! $hasEditorTests and $this->logMissingEditorTests) {
                            die("Failed to find Editor Tests: $package->path");
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

