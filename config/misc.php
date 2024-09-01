<?php
declare(strict_types = 1);

use Slothsoft\Devtools\Misc\Update\Group;
use Slothsoft\Devtools\Misc\Update\ProjectDatabase;
use Slothsoft\Devtools\Misc\Update\ProjectManager;

$workspace = realpath('/Misc');
if (! $workspace) {
    return;
}

$csharps = [
    [
        'name' => 'Pyrdacor.FreeSerf.net',
        'repository' => 'https://github.com/Pyrdacor/freeserf.net'
    ],
    [
        'name' => 'Pyrdacor.Ambermoon.net',
        'repository' => 'https://github.com/Pyrdacor/Ambermoon.net'
    ],
    [
        'name' => 'Rare.Banjo-Kazooie',
        'repository' => 'https://gitlab.com/banjo.decomp/banjo-kazooie'
    ],
    [
        'name' => 'Nintendo.SuperMario64',
        'repository' => 'https://github.com/n64decomp/sm64'
    ],
    [
        'name' => 'Slothsoft.CI-Server',
        'repository' => 'https://github.com/Faulo/gil-server'
    ]
];

$group = new Group('misc');

$manager = new ProjectManager('misc.csharp', $workspace, 'git');

foreach ($csharps as $csharp) {
    $manager->projects[] = $manager->createProject($csharp);
}

$group->groups[] = $manager;

ProjectDatabase::instance()->groups[] = $group;

