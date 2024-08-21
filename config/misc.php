<?php
declare(strict_types = 1);

use Slothsoft\Devtools\Misc\Update\Group;
use Slothsoft\Devtools\Misc\Update\ProjectDatabase;
use Slothsoft\Devtools\Misc\Update\ProjectManager;

$workspace = realpath('/CSharp');

$csharps = [
    [
        'name' => 'Pyrdacor.FreeSerf.net',
        'repository' => 'https://github.com/Pyrdacor/freeserf.net'
    ],
    [
        'name' => 'Pyrdacor.Ambermoon.net',
        'repository' => 'https://github.com/Pyrdacor/Ambermoon.net'
    ]
];

$group = new Group('misc');

$manager = new ProjectManager('misc.csharp', $workspace, 'git');

foreach ($csharps as $csharp) {
    $manager->projects[] = $manager->createProject($csharp);
}

$group->groups[] = $manager;

ProjectDatabase::instance()->groups[] = $group;

