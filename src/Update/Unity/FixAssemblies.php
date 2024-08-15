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

    public bool $logGUIDs = true;

    public bool $logAssemblies = true;

    public array $assembliesToLog = [
        'Ulisses.Core.Binding.Utilities.RuntimeUtility'
    ];

    public function __construct(string $scope) {
        $this->scope = $scope;
    }

    private $defineConstraintsEditor = [
        'UNITY_INCLUDE_TESTS'
    ];

    private $defineConstraintsRuntime = [
        'UNITY_INCLUDE_TESTS'
    ];

    private $defineConstraintsUtilities = [
        'UNITY_EDITOR'
    ];

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
                            $assemblyName = pathinfo($assemblyPath, PATHINFO_FILENAME);
                            if ($assembly = Utils::readJson($assemblyPath)) {
                                $previous = $assembly;
                                $hasChanged = $this->alwaysSave;

                                $assembly['name'] = $assemblyName;
                                $assembly['autoReferenced'] = true;

                                if (! isset($assembly['references'])) {
                                    $assembly['references'] = [];
                                    $hasChanged = true;
                                }
                                if (ReferenceSorter::sortAssemblies($assembly['references'])) {
                                    $hasChanged = true;
                                }

                                if ($assembly !== $previous) {
                                    $hasChanged = true;
                                }

                                $this->logWrongAssemblies($assemblyPath, $assembly['references']);

                                if ($hasChanged) {
                                    Utils::writeJson($assemblyPath, $assembly, 4);
                                }
                            }
                        }

                        foreach (glob('Tests/*/*.asmdef') as $assemblyPath) {
                            $assemblyName = pathinfo($assemblyPath, PATHINFO_FILENAME);
                            if ($assembly = Utils::readJson($assemblyPath)) {
                                $previous = $assembly;
                                $hasChanged = $this->alwaysSave;

                                $assembly['name'] = $assemblyName;
                                $assembly['autoReferenced'] = false;
                                $assembly['excludePlatforms'] = [];
                                $assembly['overrideReferences'] = true;

                                if (! isset($assembly['references'])) {
                                    $assembly['references'] = [];
                                    $hasChanged = true;
                                }

                                $type = pathinfo($assemblyPath, PATHINFO_FILENAME);
                                $type = explode('.', $type);
                                $type = end($type);

                                switch ($type) {
                                    case 'Editor':
                                        $hasEditorTests = true;

                                        $assembly['defineConstraints'] = $this->defineConstraintsEditor;

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
                                        break;
                                    case 'Runtime':
                                        $assembly['defineConstraints'] = $this->defineConstraintsRuntime;
                                        $assembly['includePlatforms'] = [];
                                        break;
                                    case 'Utilities':
                                        $assembly['defineConstraints'] = $this->defineConstraintsUtilities;
                                        $assembly['includePlatforms'] = [];
                                        break;
                                }

                                if (ReferenceSorter::sortAssemblies($assembly['references'])) {
                                    $hasChanged = true;
                                }

                                foreach ($this->precompiledReferences as $reference) {
                                    if (! in_array($reference, $assembly['precompiledReferences'])) {
                                        $assembly['precompiledReferences'][] = $reference;
                                        $hasChanged = true;
                                    }
                                }
                                sort($assembly['precompiledReferences']);

                                if ($assembly !== $previous) {
                                    $hasChanged = true;
                                }

                                $this->logWrongAssemblies($assemblyPath, $assembly['references']);

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

    private function logWrongAssemblies(string $assemblyPath, array $references): void {
        if ($this->logGUIDs) {
            foreach ($references as $ref) {
                if (strpos($ref, 'GUID:') === 0) {
                    die("Found '$ref' in assembly: $assemblyPath");
                }
            }
        }

        if ($this->logAssemblies) {
            foreach ($this->assembliesToLog as $ref) {
                if (in_array($ref, $references, true)) {
                    die("Found '$ref' in assembly: $assemblyPath");
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

