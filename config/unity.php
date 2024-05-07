<?php
declare(strict_types = 1);

use Slothsoft\Devtools\Misc\Update\ProjectDatabase;
use Slothsoft\Devtools\Misc\Update\UnityProjectManager;
use Slothsoft\Devtools\Misc\Update\Unity\UnityUpdateFactory;

$gameJams = [
    'GameJam.BackToTheChicken',
    'GameJam.BackToTheChicken',
    'GameJam.BattleOfTheGods',
    'GameJam.BrieYourself',
    'GameJam.CatsInCostumes',
    'GameJam.ChartRunner',
    'GameJam.Communiganda',
    'GameJam.FindingHome',
    'GameJam.FollowYourDreams',
    // 'GameJam.HeartbeatForAll',
    'GameJam.IAmSusi',
    'GameJam.KeepEverybodyHappy',
    'GameJam.MizuKiri',
    'GameJam.NanoMixture',
    'GameJam.NuttinToLose',
    'GameJam.Orbital',
    'GameJam.Pengwing',
    'GameJam.ReverseSlender',
    'GameJam.RootRush',
    'GameJam.SheepThrills',
    'GameJam.SonarUndDochSoFern',
    'GameJam.SpaceCape',
    'GameJam.SpillTheTea',
	'GameJam.SuperManual64',
    'GameJam.WatchOut',
    'GameJam.ZooSmashBaseballBash'
];

$projects = [
    'CursedCreations.CursedBroom',
    'Slothsoft.Freeblob'
];

$groups = [
    'gamejam' => $gameJams,
    'project' => $projects
];

$manager = new UnityProjectManager('unity', 'R:\\Unity', 'git');
$manager->updateFactories[] = new UnityUpdateFactory();

foreach ($groups as $key => $val) {
    $manager->addGroup($key, $val);
}

ProjectDatabase::instance()->groups[] = $manager;