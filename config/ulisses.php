<?php
declare(strict_types = 1);

use Slothsoft\Core\FileSystem;
use Slothsoft\Devtools\Misc\Update\ProjectDatabase;
use Slothsoft\Devtools\Misc\Update\UnityProjectManager;
use Slothsoft\Devtools\Misc\Update\StaticFolder\StaticFolderFactory;
use Slothsoft\Devtools\Misc\Update\Unity\FixManifest;
use Slothsoft\Devtools\Misc\Update\Unity\FixPackages;
use Slothsoft\Devtools\Misc\Update\Unity\UnityUpdateFactory;

$thirdPartyPackages = [
    'com.acegikmo.shapes',
    'com.arongranberg.astar',
    'com.kronnect.volumetric-fog-mist-2',
    'com.longbunnylabs.boing-kit',
    'jp.magicasoft.magicacloth'
];

$corePackages = [
    'Ulisses.Core.AssetManagement',
    'Ulisses.Core.AudioService',
    'Ulisses.Core.Binding',
    'Ulisses.Core.Binding.Utilities',
    'Ulisses.Core.CameraService',
    'Ulisses.Core.Database',
    'Ulisses.Core.FolderCreator',
    'Ulisses.Core.GameServices',
    'Ulisses.Core.InteractableSystem',
    'Ulisses.Core.Localization',
    'Ulisses.Core.Logging',
    'Ulisses.Core.Mesh2D',
    'Ulisses.Core.NodeGraph',
    'Ulisses.Core.Options',
    'Ulisses.Core.Persistence',
    'Ulisses.Core.PlatformManager',
    'Ulisses.Core.Random',
    'Ulisses.Core.UIStateService',
    'Ulisses.Core.UIToolkit',
    'Ulisses.Core.UnityUI',
    'Ulisses.Core.Utilities'
];

$hexxenPackages = [
    'Ulisses.HeXXen1733.Animations',
    'Ulisses.HeXXen1733.Art',
    'Ulisses.HeXXen1733.ArtDevelopment',
    'Ulisses.HeXXen1733.ArtStaging',
    'Ulisses.HeXXen1733.Audio',
    'Ulisses.HeXXen1733.Battle',
    'Ulisses.HeXXen1733.BattleDirection',
    'Ulisses.HeXXen1733.Build',
    'Ulisses.HeXXen1733.Camera',
    'Ulisses.HeXXen1733.Character.Franziska',
    'Ulisses.HeXXen1733.Character.Goetz',
    'Ulisses.HeXXen1733.Character.Irina',
    'Ulisses.HeXXen1733.Character.Magnus',
    'Ulisses.HeXXen1733.Character.NonPlayerCharacter',
    'Ulisses.HeXXen1733.CharacterController',
    'Ulisses.HeXXen1733.DataModels',
    'Ulisses.HeXXen1733.Dialog',
    'Ulisses.HeXXen1733.Enemies',
    'Ulisses.HeXXen1733.Essentials',
    'Ulisses.HeXXen1733.Exploration',
    'Ulisses.HeXXen1733.Exploration.Camera',
    'Ulisses.HeXXen1733.InputActionAsset',
    'Ulisses.HeXXen1733.Interactables.Cinematics',
    'Ulisses.HeXXen1733.Interactables.Pickups',
    'Ulisses.HeXXen1733.LevelGeneration',
    'Ulisses.HeXXen1733.LevelLayout',
    'Ulisses.HeXXen1733.Locales',
    'Ulisses.HeXXen1733.MissionGeneration',
    // 'Ulisses.HeXXen1733.Module.HeXXenHunters',
    'Ulisses.HeXXen1733.NewGame',
    'Ulisses.HeXXen1733.Options',
    'Ulisses.HeXXen1733.Prerequisites',
    'Ulisses.HeXXen1733.Sandbox.Exploration',
    'Ulisses.HeXXen1733.SharedUI',
    'Ulisses.HeXXen1733.Staging',
    'Ulisses.HeXXen1733.System',
    'Ulisses.HeXXen1733.UserInterface',
    'Ulisses.HeXXen1733.VFX',
    'Ulisses.HeXXen1733.Village'
];

$projects = [
    'Ulisses.HeXXen1733.Game',
    'Ulisses.HeXXen1733.Gamescom2023Demo',
    'Ulisses World ISB'
];

$groups = [
    'third-party' => $thirdPartyPackages,
    'core' => $corePackages,
    'hexxen' => $hexxenPackages,
    'project' => $projects
];

$projectManifestRegistries = json_decode(<<<EOT
[
    {
      "name": "Ulisses",
      "url": "http://packages.ulisses-spiele.de:4873",
      "scopes": [
        "de.ulisses-spiele.core",
        "de.ulisses-spiele.hexxen1733",
        "com.rlabrecque.steamworks.net",
        "com.esotericsoftware.spine",
        "com.longbunnylabs.boing-kit",
        "jp.magicasoft.magicacloth",
        "com.arongranberg.astar",
        "com.olegknyazev.softmask",
        "com.acegikmo.shapes",
		"com.ecasillas.missingrefsfinder",
		"com.quickeye.icon-browser",
        "com.kronnect.volumetric-fog-mist-2"
      ]
    },
    {
      "name": "Node Package Manager",
      "url": "https://registry.npmjs.com",
      "scopes": [
        "com.hecomi"
      ]
    },
    {
      "name": "OpenUPM",
      "url": "https://package.openupm.com",
      "scopes": [
        "net.slothsoft.unity-extensions",
        "net.tnrd.nsubstitute"
      ]
    }
]
EOT, true);

$projectManifestDependencies = [
    "com.unity.ide.rider" => "3.0.28",
    "com.unity.ide.visualstudio" => "2.0.22",
    "net.slothsoft.unity-extensions" => "3.1.0"
];
$projectManifestForbidden = [
    "com.unity.test-framework",
    "unity-dependencies-hunter"
];
$packageManifestDependencies = [
    "de.ulisses-spiele.core.utilities" => "4.9.6",
    "com.unity.test-framework" => "2.0.1-exp.2"
];
$packageManifestForbidden = [
    "com.unity.ide.rider",
    "com.unity.ide.visualstudio",
    "net.slothsoft.unity-extensions"
];

$manager = new UnityProjectManager('ulisses', 'R:\\Ulisses', 'plastic');

$unityUpdates = new UnityUpdateFactory();
$unityUpdates->addUpdate('fix-manifest', new FixManifest($projectManifestRegistries, $projectManifestDependencies, $projectManifestForbidden));
$packages = new FixPackages('de.ulisses-spiele', $packageManifestDependencies, $packageManifestForbidden);
$packages->setAuthor('Ulisses Digital');
$packages->setUnity('2022.3');
$packages->addForbiddenDependencyForScope('de.ulisses-spiele.core.logging', ['de.ulisses-spiele.core.utilities']);
$unityUpdates->addUpdate('fix-packages', $packages);
$manager->updateFactories[] = $unityUpdates;

$staticUpdates = new StaticFolderFactory();
$staticUpdates->addCopy('copy-devops', 'static/ulisses/devops');
$staticUpdates->addCopy('copy-devops-third-party', 'static/ulisses/devops-third-party');
$staticUpdates->addCopy('copy-git', 'static/ulisses/git');
$staticUpdates->addCopy('copy-plastic', 'static/ulisses/plastic');
$staticUpdates->addCopy('copy-unity', 'static/ulisses/unity-2022');
$manager->updateFactories[] = $staticUpdates;

foreach ($groups as $key => $val) {
    $manager->addGroup($key, $val);
}

ProjectDatabase::instance()->groups[] = $manager;

$projects = [
    'Ulisses.DSK'
];

$groups = [
    'project' => $projects
];

$manager = new UnityProjectManager('ulisses', 'R:\\Ulisses', 'git');
$manager->updateFactories[] = new UnityUpdateFactory();

foreach ($groups as $key => $val) {
    $manager->addGroup($key, $val);
}

ProjectDatabase::instance()->groups[] = $manager;

return;

$packages = [
    'Ulisses.HeXXen1733.System'
];

$ulissesPackageManifest = [
    'unity' => '2022.3',
    'author' => 'Ulisses Digital',
    'dependencies' => [
        'de.ulisses-spiele.core.utilities' => '4.9.4',
        'com.unity.test-framework' => '2.0.1-exp.2'
    ]
];

$thirdPartyPackageManifest = [
    'unity' => '2022.3',
    'dependencies' => [
        'com.unity.test-framework' => '2.0.1-exp.2'
    ]
];

$updateWorkspace = false;
$deleteIdea = false;
$copyDevOps = false;
$updateProjectManifest = true;
$updatePackageManifest = true;
$updatePackageAssemblies = true;
$updatePackageTests = true;
$abortOnNoEditor = false;
$runDotnetFormat = false;
$runTests = false;
$createSolution = false;
$doFinalCommit = false;
$onlyDoOne = false;

$updatePackage = ($updatePackageManifest or $updatePackageAssemblies);

const TARGET_DIRECTORY = 'R:\\Ulisses';

const TEMPLATE_DIRECTORY = 'template';

const TEMPLATE_DIRECTORY_THIRD_PARTY = 'template-third-party';

const FILE_PACKAGE_ASSET_VALIDATION = 'PackageAssetValidation.cs';

const CLASS_PACKAGE_ASSET_VALIDATION = <<<EOT
using System.IO;
using NUnit.Framework;
using Ulisses.Core.Utilities.Editor;

namespace %s {
    [TestFixture]
    internal sealed class PackageAssetValidation : AssetValidationBase<PackageAssetValidation.AssetSource> {
        internal sealed class AssetSource : AssetSourceBase {
            protected override string RootDirectory => Path.Combine("Packages", AssemblyInfo.ID);
        }
    }
}
EOT;

$cwd = realpath(getcwd()) or die('missing cwd');

$workspace = realpath(TARGET_DIRECTORY) or die('missing workspace');
$template = realpath(TEMPLATE_DIRECTORY) or die('missing template');
$templateThirdParty = realpath(TEMPLATE_DIRECTORY_THIRD_PARTY) or die('missing 3rd party template');

foreach ($packages as $package) {
    echo '# ' . $package . PHP_EOL;

    $hasErrors = false;

    $isUlisses = strpos($package, 'Ulisses') === 0;
    $isUlissesCore = strpos($package, 'Ulisses.Core') === 0;
    $isUlissesHexxen = strpos($package, 'Ulisses.HeXXen1733') === 0;

    chdir($workspace);

    passthru("cm workspace create \"$package\" --server=UlissesDigital@cloud");

    $target = realpath($package) or die('missing package');

    chdir($target);

    if ($updateWorkspace) {
        echo '## updating workspace...' . PHP_EOL;

        passthru("cm update");
        passthru("cm undo . -r");

        echo 'done!' . PHP_EOL;
        sleep(1);
    }

    if ($deleteIdea) {
        echo '## deleting .idea...' . PHP_EOL;

        if ($idea = realpath('.idea')) {
            passthru(sprintf('rd /s /q %s', escapeshellarg($idea)));
            passthru('cm ci --all -c="Delete .idea"');
        }

        echo 'done!' . PHP_EOL;
        sleep(1);
    }

    if ($copyDevOps) {
        echo '## copying DevOps files...' . PHP_EOL;

        passthru(sprintf('xcopy %s %s /c /e /i /h /r /k /y /v', escapeshellarg($template), escapeshellarg($target)));
        if ($isUlisses) {} else {
            passthru(sprintf('xcopy %s %s /c /e /i /h /r /k /y /v', escapeshellarg($templateThirdParty), escapeshellarg($target)));
        }
        passthru('cm add *');
        passthru('cm ci --all -c="Update DevOps files"');

        echo 'done!' . PHP_EOL;
        sleep(5);
    }

    if ($updateProjectManifest) {
        echo '## updating project manifest...' . PHP_EOL;

        $manifestPath = realpath('Packages/manifest.json') or die('failed to find manifest.json');
        $manifest = json_decode(file_get_contents($manifestPath), true) or die('Failed to parse manifest.json');

        if (! isset($manifest['dependencies'])) {
            $manifest['dependencies'] = [];
        }
        $manifest['dependencies'] = array_merge($manifest['dependencies'], $projectManifestDependencies);

        foreach ($projectManifestForbidden as $key) {
            unset($manifest['dependencies'][$key]);
        }

        ksort($manifest['dependencies']);

        $manifest['scopedRegistries'] = $projectManifestRegistries;
        if (! stripos($manifestPath . implode('', array_keys($manifest['dependencies'])), 'hexxen1733')) {
            $manifest['scopedRegistries'][0]['scopes'] = array_values(array_diff($manifest['scopedRegistries'][0]['scopes'], [
                "de.ulisses-spiele.hexxen1733"
            ]));
        }

        ksort($manifest);

        file_put_contents($manifestPath, str_replace('    ', '  ', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) . "\n");
        touch($manifestPath);

        passthru('cm ci --all -c="Update manifest.json"');

        echo 'done!' . PHP_EOL;
        sleep(1);
    }

    if ($updatePackage) {
        echo '## updating packages...' . PHP_EOL;

        foreach (FileSystem::scanDir('Packages', FileSystem::SCANDIR_EXCLUDE_FILES) as $directory) {
            if (strpos($directory, '.') and $manifestPath = realpath("Packages/$directory/package.json")) {
                echo $directory . PHP_EOL;

                chdir("Packages/$directory");
                if ($updatePackageManifest) {
                    $manifest = json_decode(file_get_contents($manifestPath), true) or die('Failed to parse package.json');
                    $packageManifest = $isUlisses ? $ulissesPackageManifest : $thirdPartyPackageManifest;
                    foreach ($packageManifest as $key => $value) {
                        $manifest[$key] = is_array($value) ? array_merge($manifest[$key] ?? [], $value) : $value;
                    }
                    if (! isset($manifest['dependencies'])) {
                        $manifest['dependencies'] = [];
                    }
                    if (isset($manifest['dependencies'][$manifest['name']])) {
                        unset($manifest['dependencies'][$manifest['name']]);
                    }
                    ksort($manifest['dependencies']);
                    file_put_contents($manifestPath, str_replace('    ', '  ', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)));
                    touch($manifestPath);
                }

                if ($updatePackageAssemblies and $isUlisses) { // */Ulisses.*.Tests.Editor
                    $editorAssemblyPath = null;
                    foreach (glob('Tests/*/*.asmdef') as $assemblyPath) {
                        echo $assemblyPath . PHP_EOL;
                        $assembly = json_decode(file_get_contents($assemblyPath), true) or die('Failed to parse package.json');

                        if (strpos($assemblyPath, '.Tests.Editor.')) {
                            $editorAssemblyPath = $assemblyPath;
                            if ($updatePackageTests) {
                                if (! in_array("Ulisses.Core.Utilities.Editor", $assembly['references'])) {
                                    $assembly['references'][] = "Ulisses.Core.Utilities.Editor";
                                }

                                $testsFolder = dirname($assemblyPath);
                                $testsClass = sprintf(CLASS_PACKAGE_ASSET_VALIDATION, $assembly['name']);
                                file_put_contents($testsFolder . DIRECTORY_SEPARATOR . FILE_PACKAGE_ASSET_VALIDATION, $testsClass);
                            }

                            $assembly['includePlatforms'] = [
                                "Editor"
                            ];
                        } else {
                            $assembly['includePlatforms'] = [];
                        }

                        $assembly['excludePlatforms'] = [];
                        $assembly['overrideReferences'] = true;
                        if (! in_array('nunit.framework.dll', $assembly['precompiledReferences'])) {
                            $assembly['precompiledReferences'][] = "nunit.framework.dll";
                        }
                        $assembly['autoReferenced'] = false;
                        if (! in_array('UNITY_INCLUDE_TESTS', $assembly['defineConstraints'])) {
                            $assembly['defineConstraints'][] = "UNITY_INCLUDE_TESTS";
                        }

                        file_put_contents($assemblyPath, json_encode($assembly, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                        touch($assemblyPath);
                    }

                    if ($abortOnNoEditor and ! $editorAssemblyPath) {
                        die('failed to find Tests.Editor assembly in:' . PHP_EOL . $target);
                    }
                }
                chdir($target);
            }
        }

        passthru('cm ci --all -c="Update package"');
        echo 'done!' . PHP_EOL;
        sleep(1);
    }

    if ($runDotnetFormat) {
        echo '## running dotnet-format...' . PHP_EOL;
        passthru('dotnet-format.bat');
        echo 'done!' . PHP_EOL;
    }

    if ($runTests) {
        echo '## running tests...' . PHP_EOL;

        chdir($cwd);
        $result = 0;
        passthru(sprintf('composer exec unity-tests %s EditMode', escapeshellarg($target)), $result);

        if ($result !== 0) {
            $hasErrors = true;
        }

        chdir($target);
        echo 'done!' . PHP_EOL;
        sleep(1);
    }

    if ($createSolution) {
        echo '## creating solution...' . PHP_EOL;

        chdir($cwd);
        $result = 0;
        passthru(sprintf('composer exec unity-method %s Slothsoft.UnityExtensions.Editor.Build.Solution', escapeshellarg($target)), $result);

        if ($result !== 0) {
            $hasErrors = true;
        }

        chdir($target);
        echo 'done!' . PHP_EOL;
        sleep(1);
    }

    if ($doFinalCommit) {
        echo '## saving changed files...' . PHP_EOL;

        if ($hasErrors) {
            echo 'a previous step failed, refusing to commit' . PHP_EOL;
        } else {
            passthru('cm add Packages -R');
            passthru('cm ci --all -c="Update project"');
        }

        echo 'done!' . PHP_EOL;
        sleep(5);
    }

    echo PHP_EOL;

    if ($onlyDoOne) {
        break;
    }
}