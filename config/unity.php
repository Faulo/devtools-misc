<?php
declare(strict_types = 1);

use Slothsoft\Devtools\Misc\Update\ProjectDatabase;
use Slothsoft\Devtools\Misc\Update\UnityProjectManager;

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
    'GameJam.WatchOut',
    'GameJam.ZooSmashBaseballBash'
];

$projects = [
    'CursedCreations.CursedBroom'
];

$groups = [
    'gamejam' => $gameJams,
    'project' => $projects
];

$manager = new UnityProjectManager('unity', 'R:\\Unity');

foreach ($groups as $key => $val) {
    $manager->addGroup($key, $val);
}

ProjectDatabase::instance()->groups[] = $manager;