<?php
declare(strict_types = 1);

use Slothsoft\Core\Calendar\Seconds;
use Slothsoft\Devtools\Misc\Utils;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\ProjectDatabase;
use Slothsoft\Devtools\Misc\Update\UnityProjectManager;
use Slothsoft\Devtools\Misc\Update\StaticFolder\StaticFolderFactory;
use Slothsoft\Devtools\Misc\Update\Unity\UnityUpdateFactory;
use Slothsoft\Unity\UnityHub;

UnityHub::setProcessTimeout(2 * Seconds::HOUR);
UnityHub::setProcessTimeout(10 * Seconds::MINUTE);

$workspace = Utils::ensurePath(getenv('UserProfile') . '/Desktop', 'Unity');

$gameJams = [
    [
        'name' => 'GameJam.BackToTheChicken',
        'repository' => 'https://github.com/Faulo/BackToTheChicken'
    ],
    [
        'name' => 'GameJam.BattleOfTheGods',
        'repository' => 'https://github.com/Faulo/BattleOfTheGods'
    ],
    [
        'name' => 'GameJam.BrieYourself',
        'repository' => 'https://github.com/Faulo/BrieYourself'
    ],
    [
        'name' => 'GameJam.CatsInCostumes',
        'repository' => 'https://github.com/KaddaStrophe/GGJ24_CatsInCostumes'
    ],
    [
        'name' => 'GameJam.ChartRunner',
        'repository' => 'https://github.com/Faulo/ChartRunner'
    ],
    [
        'name' => 'GameJam.Communiganda',
        'repository' => 'https://github.com/Faulo/Communiganda'
    ],
    [
        'name' => 'GameJam.FindingHome',
        'repository' => 'https://github.com/Faulo/FindingHome'
    ],
    [
        'name' => 'GameJam.FollowYourDreams',
        'repository' => 'https://github.com/Faulo/FollowYourDreams'
    ],
    [
        'name' => 'GameJam.HeartbeatForAll',
        'repository' => 'https://github.com/Faulo/HeartbeatForAll'
    ],
    [
        'name' => 'GameJam.IAmSusi',
        'repository' => 'https://github.com/Glowdragon/i-am-susi-game'
    ],
    [
        'name' => 'GameJam.KeepEverybodyHappy',
        'repository' => 'https://github.com/Faulo/KeepEverybodyHappy'
    ],
    [
        'name' => 'GameJam.LostNotes',
        'repository' => 'https://github.com/lowkey42/lost_notes'
    ],
    [
        'name' => 'GameJam.MizuKiri',
        'repository' => 'https://github.com/Faulo/MizuKiri'
    ],
    [
        'name' => 'GameJam.NanoMixture',
        'repository' => 'https://github.com/Faulo/NanoMixture'
    ],
    [
        'name' => 'GameJam.NuttinToLose',
        'repository' => 'https://github.com/Faulo/NuttinToLose'
    ],
    [
        'name' => 'GameJam.Orbital',
        'repository' => 'https://github.com/Faulo/Orbital'
    ],
    [
        'name' => 'GameJam.Pengwing',
        'repository' => 'https://github.com/Faulo/Pengwing'
    ],
    [
        'name' => 'GameJam.ReverseSlender',
        'repository' => 'https://github.com/Faulo/ReverseSlender'
    ],
    [
        'name' => 'GameJam.RootRush',
        'repository' => 'https://github.com/nicoexport/Rootlesnake'
    ],
    [
        'name' => 'GameJam.SheepThrills',
        'repository' => 'https://github.com/Faulo/SheepThrills'
    ],
    [
        'name' => 'GameJam.SnailsInTheSky',
        'repository' => 'https://github.com/Faulo/SnailsInTheSky'
    ],
    [
        'name' => 'GameJam.SonarUndDochSoFern',
        'repository' => 'https://github.com/Faulo/SonarUndDochSoFern'
    ],
    [
        'name' => 'GameJam.SpaceCape',
        'repository' => 'https://github.com/Faulo/SpaceCape'
    ],
    [
        'name' => 'GameJam.SpillTheTea',
        'repository' => 'https://github.com/Faulo/SpillTheTea'
    ],
    [
        'name' => 'GameJam.SuperManual64',
        'repository' => 'https://github.com/Faulo/SuperManual64'
    ],
    [
        'name' => 'GameJam.WatchOut',
        'repository' => 'https://github.com/Faulo/WatchOut'
    ],
    [
        'name' => 'GameJam.DokoDont',
        'repository' => 'https://github.com/Faulo/DokoDont'
    ],
    [
        'name' => 'GameJam.LiarLiarForestFire',
        'repository' => 'https://github.com/Faulo/LiarLiarForestFire'
    ],
    [
        'name' => 'GameJam.ZooSmashBaseballBash',
        'repository' => 'https://github.com/Faulo/ZooSmashBaseballBash'
    ],
    [
        'name' => 'GameJam.SnailsOnTheRoad',
        'repository' => 'https://github.com/Faulo/SnailsOnTheRoad'
    ]
];

$projects = [
    [
        'name' => 'Slothsoft.CritterGrove',
        'repository' => 'https://github.com/Faulo/CritterGrove'
    ],
    [
        'name' => 'Slothsoft.Freeblob',
        'repository' => 'https://github.com/Faulo/Freeblob'
    ],
    [
        'name' => 'Slothsoft.ExpositionOfExtraordinaryExperiences',
        'repository' => 'https://github.com/Faulo/ExpositionOfExtraordinaryExperiences'
    ],
    [
        'name' => 'Oilcatz.PowerFantasyVR',
        'repository' => 'https://github.com/Faulo/PowerFantasyVR'
    ],
    [
        'name' => 'Oilcatz.TrialOfTwo',
        'repository' => 'https://github.com/Faulo/TrialOfTwo'
    ],
    [
        'name' => 'Slothsoft.CursedBroom',
        'repository' => 'https://github.com/Faulo/CursedBroom'
    ]
];

$groups = [
    'gamejam' => $gameJams,
    'project' => $projects
];

$manager = new UnityProjectManager('unity', $workspace, 'git');
$manager->updateFactories[] = new UnityUpdateFactory();

$staticUpdates = new StaticFolderFactory();
$staticUpdates->addCopyWithSwitch('copy-unity', function (Project $project): ?string {
    $hub = UnityHub::getInstance();
    $unity = $hub->findProject($project->workspace, false);
    if ($unity) {
        $version = $unity->getEditorVersion();
        $version = explode('.', $version)[0];
        $folder = __DIR__ . '/../static/unity/unity-' . $version;
        if (! is_dir($folder)) {
            throw new \Exception("Unsupported Unity version '{$unity->getEditorVersion()}'!");
        }
        return $folder;
    }
    return null;
});
$manager->updateFactories[] = $staticUpdates;

foreach ($groups as $key => $val) {
    $manager->addGroup($key, $val);
}

ProjectDatabase::instance()->groups[] = $manager;