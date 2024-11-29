<?php
declare(strict_types = 1);

use Slothsoft\Devtools\Misc\Utils;
use Slothsoft\Devtools\Misc\Update\Group;
use Slothsoft\Devtools\Misc\Update\ProjectDatabase;
use Slothsoft\Devtools\Misc\Update\ProjectManager;

$workspace = Utils::ensurePath(getenv('UserProfile') . '/Desktop', 'Misc');

$csharps = [

    [
        'name' => 'ExOK.Celeste64',
        'repository' => 'https://github.com/ExOK/Celeste64'
    ],
    [
        'name' => 'Neversoft.TonyHawksUnderground',
        'repository' => 'https://github.com/RetailGameSourceCode/TonyHawksUnderground'
    ],
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
        'name' => 'Nintendo.MajorasMask',
        'repository' => 'https://github.com/zeldaret/mm/'
    ],
    [
        'name' => 'Nintendo.SuperMario64',
        'repository' => 'https://github.com/n64decomp/sm64'
    ],
    [
        'name' => 'Slothsoft.AdventOfCode',
        'repository' => 'https://github.com/Faulo/AdventOfCode'
    ],
    [
        'name' => 'Slothsoft.ValheimMods',
        'repository' => 'https://github.com/Faulo/ValheimMods'
    ],
    [
        'name' => 'Slothsoft.SuperMario64',
        'repository' => 'https://github.com/Faulo/sm64ex'
    ]
];

$group = new Group('misc');

$manager = new ProjectManager('misc.csharp', $workspace, 'git');

foreach ($csharps as $csharp) {
    $manager->projects[] = $manager->createProject($csharp);
}

$group->groups[] = $manager;

ProjectDatabase::instance()->groups[] = $group;

