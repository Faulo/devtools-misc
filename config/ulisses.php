<?php
declare(strict_types = 1);

use Slothsoft\Devtools\Misc\Utils;
use Slothsoft\Devtools\Misc\Update\Group;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\ProjectDatabase;
use Slothsoft\Devtools\Misc\Update\ProjectManager;
use Slothsoft\Devtools\Misc\Update\StubUpdate;
use Slothsoft\Devtools\Misc\Update\UnityProjectManager;
use Slothsoft\Devtools\Misc\Update\UpdateFactory;
use Slothsoft\Devtools\Misc\Update\Fix\FixJenkins;
use Slothsoft\Devtools\Misc\Update\Plastic\TagCommit;
use Slothsoft\Devtools\Misc\Update\StaticFolder\StaticFolderFactory;
use Slothsoft\Devtools\Misc\Update\Unity\AddPackagesToProject;
use Slothsoft\Devtools\Misc\Update\Unity\CallMethod;
use Slothsoft\Devtools\Misc\Update\Unity\FixAssemblies;
use Slothsoft\Devtools\Misc\Update\Unity\FixChangelog;
use Slothsoft\Devtools\Misc\Update\Unity\FixManifest;
use Slothsoft\Devtools\Misc\Update\Unity\FixPackages;
use Slothsoft\Devtools\Misc\Update\Unity\UnityUpdateFactory;
use Slothsoft\Unity\UnityPackageInfo;
use Slothsoft\Unity\UnityProjectInfo;

$workspace = realpath('/Ulisses');

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
    'Ulisses.Core.CaptureCamera',
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
    // 'Ulisses.HeXXen1733.Animations',
    'Ulisses.HeXXen1733.Art',
    'Ulisses.HeXXen1733.ArtDevelopment',
    // 'Ulisses.HeXXen1733.ArtStaging',
    'Ulisses.HeXXen1733.Audio',
    'Ulisses.HeXXen1733.Battle',
    // 'Ulisses.HeXXen1733.BattleDirection',
    'Ulisses.HeXXen1733.Build',
    'Ulisses.HeXXen1733.Camera',
    'Ulisses.HeXXen1733.Character.Abbas',
    'Ulisses.HeXXen1733.Character.Aveline',
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
    'Ulisses.HeXXen1733.GameMaster',
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
    // 'Ulisses.HeXXen1733.UserInterface',
    'Ulisses.HeXXen1733.VFX',
    'Ulisses.HeXXen1733.Village'
];

$projects = [
    'Ulisses.HeXXen1733.Game',
    'Ulisses.HeXXen1733.Gamescom2023Demo',
    'Ulisses World ISB'
];

$sandboxes = [
    'Ulisses.Sandbox.Core',
    'Ulisses.Sandbox.HeXXen1733'
];

$groups = [
    'third-party' => $thirdPartyPackages,
    'core' => $corePackages,
    'hexxen1733' => $hexxenPackages,
    'project' => $projects,
    'sandboxes' => $sandboxes
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

$artPackage = [ // "de.ulisses-spiele.hexxen1733.art" => "1.0.0-pre.10"
];
$artChangelog = <<<EOT
### Changed

- [DSZ] Changed art dependency to de.ulisses-spiele.hexxen1733.art.
EOT;

$projectManifestDependencies = [
    "com.unity.ide.rider" => "3.0.31",
    "com.unity.ide.visualstudio" => "2.0.22",
    "net.slothsoft.unity-extensions" => "3.1.0"
];
$projectManifestForbidden = [
    "com.unity.test-framework",
    "unity-dependencies-hunter",
    "de.ulisses-spiele.hexxen1733.animations"
];
$packageManifestDependencies = [
    "de.ulisses-spiele.core.utilities" => "4.19.9",
    "com.unity.test-framework" => "2.0.1-exp.2",
    // "jp.magicasoft.magicacloth" => "1.0.0",
    "net.tnrd.nsubstitute" => "5.1.0"
];
$packageManifestForbidden = [
    "com.unity.ide.rider",
    "com.unity.ide.visualstudio",
    "net.slothsoft.unity-extensions",
    "de.ulisses-spiele.hexxen1733.animations"
];
$optionalUpgrades = $artPackage + [
    // "de.ulisses-spiele.hexxen1733.art.animals" => $artPackage,
    // "de.ulisses-spiele.hexxen1733.art.characters" => $artPackage,
    // "de.ulisses-spiele.hexxen1733.art.environment" => $artPackage,
    // "de.ulisses-spiele.hexxen1733.art.foliage" => $artPackage,
    // "de.ulisses-spiele.hexxen1733.art.misc" => $artPackage,
    // "de.ulisses-spiele.hexxen1733.art.props" => $artPackage,
    // "de.ulisses-spiele.hexxen1733.art.textures" => $artPackage,
    // "de.ulisses-spiele.hexxen1733.staging" => "0.1.10-pre.3",
    // "de.ulisses-spiele.hexxen1733.shader" => "2.0.0",
    // "com.unity.render-pipelines.universal" => "14.0.11",
    // "de.ulisses-spiele.hexxen1733.battle" => "0.14.0",
    // "de.ulisses-spiele.core.logging" => "1.2.3",
    // "de.ulisses-spiele.hexxen1733.sharedui" => "1.0.0",
    "de.ulisses-spiele.hexxen1733.art" => "1.0.0"
];

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

$manager = new UnityProjectManager('ulisses', $workspace, 'plastic');

$unityUpdates = new UnityUpdateFactory();

$fix = new TagCommit('devops', "(comment = 'Update project files' or comment = 'Update DevOps files') and owner = 'daniel.schulz@ulisses-spiele.de' and not attribute = 'devops'");
$fix->setComment('If set, this changeset was auto-generated by the devops pipeline. default: devops');
$unityUpdates->addUpdate('tag-devops', $fix);

$fix = new FixManifest($projectManifestRegistries);
$fix->setRequiredDependencies($projectManifestDependencies);
$fix->setOptionalDependencies($optionalUpgrades);
$fix->setForbiddenDependencies($projectManifestForbidden);
$unityUpdates->addUpdate('fix-manifest', $fix);

$fix = new FixPackages('de.ulisses-spiele');
$fix->setRequiredDependencies($packageManifestDependencies);
$fix->setOptionalDependencies($optionalUpgrades);
$fix->setForbiddenDependencies($packageManifestForbidden);
$fix->setAuthor('Ulisses Digital');
$fix->setUnity('2022.3');
$fix->setUnityRelease('33f1');
$fix->documentationUrlDelegate = function (Project $project, UnityProjectInfo $unity, UnityPackageInfo $package): string {
    $packageId = $package->package['name'];
    return "http://packages.ulisses-spiele.de:4873/-/web/detail/$packageId/";
};
$fix->homepageUrlDelegate = function (Project $project, UnityProjectInfo $unity, UnityPackageInfo $package): string {
    $projectUrl = basename($unity->path);

    $jobUrl = 'packages.third-party';
    if (stripos($projectUrl, 'ulisses.core') === 0) {
        $jobUrl = 'packages.ulisses';
    }
    if (stripos($projectUrl, 'ulisses.hexxen1733') === 0) {
        $jobUrl = 'packages.hexxen1733';
    }

    $packageUrl = Utils::toUrl($package->package['name']);

    return "http://ci.ulisses-spiele.de:8080/job/$jobUrl/job/$projectUrl/$packageUrl/index.html";
};
$fix->changelogUrlDelegate = function (Project $project, UnityProjectInfo $unity, UnityPackageInfo $package): string {
    $projectUrl = basename($unity->path);

    $jobUrl = 'packages.third-party';
    if (stripos($projectUrl, 'ulisses.core') === 0) {
        $jobUrl = 'packages.ulisses';
    }
    if (stripos($projectUrl, 'ulisses.hexxen1733') === 0) {
        $jobUrl = 'packages.hexxen1733';
    }

    $packageUrl = Utils::toUrl($package->package['name']);

    return "http://ci.ulisses-spiele.de:8080/job/$jobUrl/job/$projectUrl/$packageUrl/CHANGELOG.html";
};
$unityUpdates->addUpdate('fix-packages', $fix);

$fix = new FixAssemblies('de.ulisses-spiele');
$fix->addEditorTestReference("Ulisses.Core.Utilities.Editor");
$fix->addEditorTestClass(FILE_PACKAGE_ASSET_VALIDATION, CLASS_PACKAGE_ASSET_VALIDATION);
$unityUpdates->addUpdate('fix-assemblies', $fix);

$fix = new FixChangelog('de.ulisses-spiele');
$fix->setChangelogForDependency("de.ulisses-spiele.hexxen1733.art", $artChangelog);
$unityUpdates->addUpdate('fix-changelog', $fix);

$fix = new AddPackagesToProject($manager->workspaceDir . 'Ulisses.Sandbox.Core');
$unityUpdates->addUpdate('fix-sandbox-core', $fix);
$fix = new AddPackagesToProject($manager->workspaceDir . 'Ulisses.Sandbox.HeXXen1733');
$unityUpdates->addUpdate('fix-sandbox-hexxen1733', $fix);

$fix = new CallMethod('Ulisses.Core.Utilities.Editor.PackageCreation.PackageUpdater.UpdateAll');
$unityUpdates->addUpdate('update', $fix);

$manager->updateFactories[] = $unityUpdates;

$staticUpdates = new StaticFolderFactory();
$staticUpdates->addDelete('delete-deployment', 'Jenkinsfile.Deployment');
$staticUpdates->addDelete('delete-vs', '.vs', '.vsconfig', 'obj');
$staticUpdates->addDelete('delete-idea', '.idea');
$staticUpdates->addDelete('delete-tmpro', 'Assets/TextMesh Pro/Resources/Fonts & Materials*', 'Assets/TextMesh Pro/Shaders*', 'Assets/TextMesh Pro/Fonts*');
$staticUpdates->addCopyWithSwitch('copy-devops', function (Project $project): ?string {
    return stripos($project->id, 'ulisses') === 0 ? __DIR__ . '/../static/ulisses/devops' : __DIR__ . '/../static/ulisses/devops-third-party';
});
$staticUpdates->addCopy('copy-git', 'static/ulisses/empty');
$staticUpdates->addCopy('copy-plastic', 'static/ulisses/plastic');
$staticUpdates->addCopy('copy-unity', 'static/ulisses/unity-2022');
$manager->updateFactories[] = $staticUpdates;

foreach ($groups as $key => $val) {
    $manager->addGroup($key, $val);
}

ProjectDatabase::instance()->groups[] = $manager;
ProjectDatabase::instance()->groups[] = (new Group('ulisses-plastic'))->withGroup($manager);

$projects = [
    [
        'name' => 'Ulisses.DSK',
        'repository' => 'https://github.com/UlissesSpieleDigital/DSK-Digital'
    ]
];

$groups = [
    'project' => $projects
];

$manager = new UnityProjectManager('ulisses', $workspace, 'git');

$unityUpdates = new UnityUpdateFactory();
$unityUpdates->addUpdate('tag-devops', new StubUpdate());
$unityUpdates->addUpdate('fix-manifest', new StubUpdate());
$unityUpdates->addUpdate('fix-packages', new StubUpdate());
$unityUpdates->addUpdate('fix-assemblies', new StubUpdate());
$unityUpdates->addUpdate('fix-changelog', new StubUpdate());
$unityUpdates->addUpdate('fix-sandbox-core', new StubUpdate());
$unityUpdates->addUpdate('fix-sandbox-hexxen1733', new StubUpdate());
$unityUpdates->addUpdate('update', new StubUpdate());
$manager->updateFactories[] = $unityUpdates;

$staticUpdates = new StaticFolderFactory();
$staticUpdates->addDelete('delete-deployment', 'Jenkinsfile.Deployment');
$staticUpdates->addDelete('delete-vs', '.vs', '.vsconfig', 'obj');
$staticUpdates->addDelete('delete-idea', '.idea');
$staticUpdates->addDelete('delete-tmpro', 'Assets/TextMesh Pro/Resources/Fonts & Materials*', 'Assets/TextMesh Pro/Shaders*', 'Assets/TextMesh Pro/Fonts*');
$staticUpdates->addCopy('copy-devops', 'static/ulisses/empty');
$staticUpdates->addCopy('copy-git', 'static/ulisses/empty');
$staticUpdates->addCopy('copy-plastic', 'static/ulisses/empty');
$staticUpdates->addCopy('copy-unity', 'static/ulisses/empty');
$manager->updateFactories[] = $staticUpdates;

foreach ($groups as $key => $val) {
    $manager->addGroup($key, $val);
}

ProjectDatabase::instance()->groups[] = $manager;
ProjectDatabase::instance()->groups[] = (new Group('ulisses-git'))->withGroup($manager);

$servers = [
    [
        'name' => 'Ulisses.CI-Server',
        'repository' => 'https://github.com/UlissesSpieleDigital/CI-Server'
    ]
];

$groups = [
    'server' => $servers
];

$manager = new ProjectManager('ulisses.server', $workspace, 'git');
$updates = new UpdateFactory();
$fix = new FixJenkins();
$fix->alwaysSave = false;
$fix->clearActions = true;
$fix->properties['org.jenkinsci.plugins.workflow.job.properties.DisableResumeJobProperty'] = true;
$fix->elements['//abortPrevious'] = 'false';
$fix->elements['//cleanup'] = 'STANDARD';
$fix->elements['//pollOnController'] = 'true';
$fix->elements['//lightweight'] = 'true';
$updates->addUpdate('fix-jobs', $fix);

$manager->updateFactories[] = $updates;

foreach ($groups as $key => $val) {
    $manager->addGroup($key, $val);
}

ProjectDatabase::instance()->groups[] = $manager;
