<?php
declare(strict_types = 1);

use Slothsoft\Devtools\Misc\Update\ProjectDatabase;
use Slothsoft\Devtools\Misc\Update\UnityProjectManager;
use Slothsoft\Devtools\Misc\Update\Unity\UnityUpdateFactory;

$workspace = realpath('/Unity');

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
        'name' => 'GameJam.ZooSmashBaseballBash',
        'repository' => 'https://github.com/Faulo/ZooSmashBaseballBash'
    ]
];

$projects = [
    [
        'name' => 'CursedCreations.CursedBroom',
        'repository' => 'https://github.com/Cursed-Creations/CursedBroom'
    ],
    [
        'name' => 'Slothsoft.Freeblob',
        'repository' => 'https://github.com/Faulo/Freeblob'
    ],
    [
        'name' => 'Slothsoft.ExpositionOfExtraordinaryExperiences',
        'repository' => 'https://github.com/Faulo/ExpositionOfExtraordinaryExperiences'
    ]
];

$groups = [
    'gamejam' => $gameJams,
    'project' => $projects
];

$manager = new UnityProjectManager('unity', $workspace, 'git');
$manager->updateFactories[] = new UnityUpdateFactory();

foreach ($groups as $key => $val) {
    $manager->addGroup($key, $val);
}

ProjectDatabase::instance()->groups[] = $manager;