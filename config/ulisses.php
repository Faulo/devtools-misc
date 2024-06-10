<?php
declare(strict_types = 1);

use Slothsoft\Devtools\Misc\Update\Group;
use Slothsoft\Devtools\Misc\Update\ProjectDatabase;
use Slothsoft\Devtools\Misc\Update\UnityProjectManager;
use Slothsoft\Devtools\Misc\Update\StaticFolder\StaticFolderFactory;
use Slothsoft\Devtools\Misc\Update\Unity\FixAssemblies;
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
    'hexxen1733' => $hexxenPackages,
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
    "de.ulisses-spiele.core.utilities" => "4.10.3",
    "com.unity.test-framework" => "2.0.1-exp.2",
    "net.tnrd.nsubstitute" => "5.1.0"
];
$packageManifestForbidden = [
    "com.unity.ide.rider",
    "com.unity.ide.visualstudio",
    "net.slothsoft.unity-extensions",
    "Packages/de.ulisses-spiele.hexxen1733.art.textures",
    "de.ulisses-spiele.hexxen1733.art.animals",
    "de.ulisses-spiele.hexxen1733.art.characters",
    "de.ulisses-spiele.hexxen1733.art.environment",
    "de.ulisses-spiele.hexxen1733.art.foliage",
    "de.ulisses-spiele.hexxen1733.art.misc",
    "de.ulisses-spiele.hexxen1733.art.props",
    "de.ulisses-spiele.hexxen1733.art.textures"
];
$optionalUpgrades = [
    "de.ulisses-spiele.hexxen1733.shader" => "1.10.4"
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

$manager = new UnityProjectManager('ulisses', realpath('R:/Ulisses'), 'plastic');

$unityUpdates = new UnityUpdateFactory();

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
$unityUpdates->addUpdate('fix-packages', $fix);

$fix = new FixAssemblies('de.ulisses-spiele');
$fix->addEditorTestReference("Ulisses.Core.Utilities.Editor");
$fix->addEditorTestClass(FILE_PACKAGE_ASSET_VALIDATION, CLASS_PACKAGE_ASSET_VALIDATION);
$unityUpdates->addUpdate('fix-assemblies', $fix);

$manager->updateFactories[] = $unityUpdates;

$staticUpdates = new StaticFolderFactory();
$staticUpdates->addDelete('delete-deployment', 'Jenkinsfile.Deployment');
$staticUpdates->addDelete('delete-vs', '.vs', '.vsconfig', 'obj');
$staticUpdates->addDelete('delete-idea', '.idea');
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
ProjectDatabase::instance()->groups[] = (new Group('ulisses-plastic'))->withGroup($manager);

$projects = [
    'Ulisses.DSK'
];

$groups = [
    'project' => $projects
];

$manager = new UnityProjectManager('ulisses', realpath('R:/Ulisses'), 'git');
$manager->updateFactories[] = new UnityUpdateFactory();

foreach ($groups as $key => $val) {
    $manager->addGroup($key, $val);
}

ProjectDatabase::instance()->groups[] = $manager;
ProjectDatabase::instance()->groups[] = (new Group('ulisses-git'))->withGroup($manager);
