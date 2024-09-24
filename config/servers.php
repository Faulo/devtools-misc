<?php
declare(strict_types = 1);

use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\ProjectDatabase;
use Slothsoft\Devtools\Misc\Update\ServerManager;
use Slothsoft\Devtools\Misc\Update\StaticFolder\StaticFolderFactory;

$workspace = realpath('/PHP');
if (! $workspace) {
    return;
}

$old = [
    [
        'name' => 'amber.slothsoft.net',
        'repository' => 'https://github.com/Faulo/server-amber.slothsoft.net',
        'homeUrl' => 'http://amber.slothsoft.net'
    ],
    [
        'name' => 'cursedcreations.slothsoft.net',
        'repository' => 'https://github.com/Cursed-Creations/server-cursedcreations.slothsoft.net',
        'homeUrl' => 'http://cursedcreations.slothsoft.net',
        'vendor' => 'cursedcreations'
    ],
    [
        'name' => 'dev.slothsoft.net',
        'repository' => 'https://github.com/Faulo/server-dev.slothsoft.net',
        'homeUrl' => 'http://dev.slothsoft.net'
    ],
    [
        'name' => 'mtg.slothsoft.net',
        'repository' => 'https://github.com/Faulo/server-mtg.slothsoft.net',
        'homeUrl' => 'http://mtg.slothsoft.net'
    ],
    [
        'name' => 'schedule.slothsoft.net',
        'repository' => 'https://github.com/Faulo/server-schedule.slothsoft.net',
        'homeUrl' => 'http://schedule.slothsoft.net'
    ],
    [
        'name' => 'schema.slothsoft.net',
        'repository' => 'https://github.com/Faulo/server-schema.slothsoft.net',
        'homeUrl' => 'http://schema.slothsoft.net'
    ],
    [
        'name' => 'test.slothsoft.net',
        'repository' => 'https://github.com/Faulo/server-slothsoft.net',
        'homeUrl' => 'http://test.slothsoft.net'
    ],
    [
        'name' => 'trialoftwo.slothsoft.net',
        'repository' => 'https://github.com/Faulo/server-trialoftwo.slothsoft.net',
        'homeUrl' => 'http://trialoftwo.slothsoft.net',
        'vendor' => 'oilcatz'
    ]
];

$new = [
    [
        'name' => 'daniel-schulz.slothsoft.net',
        'repository' => 'https://github.com/Faulo/server-daniel-schulz.slothsoft.net',
        'homeUrl' => 'http://daniel-schulz.slothsoft.net'
    ],
    [
        'name' => 'farah.slothsoft.net',
        'repository' => 'https://github.com/Faulo/server-farah.slothsoft.net',
        'homeUrl' => 'http://farah.slothsoft.net'
    ],
    [
        'name' => 'historischer-spieleabend.slothsoft.net',
        'repository' => 'https://github.com/Faulo/server-historischer-spieleabend.slothsoft.net',
        'homeUrl' => 'http://historischer-spieleabend.slothsoft.net'
    ]
];

$groups = [
    'old' => $old,
    'new' => $new
];

$manager = new ServerManager('server', $workspace);

foreach ($groups as $key => $val) {
    $manager->addGroup($key, $val);
}

$staticUpdates = new StaticFolderFactory();
$staticUpdates->addCopyWithSwitch('copy-devops', function (Project $project) use ($groups): ?string {
    $type = '';
    foreach ($groups as $key => $group) {
        foreach ($group as $id) {
            if (strcasecmp($id['name'], $project->id) === 0) {
                $type = $key;
                break 2;
            }
        }
    }

    switch ($type) {
        case 'new':
            return __DIR__ . '/../static/slothsoft/server';
    }

    return null;
});
$manager->updateFactories[] = $staticUpdates;

ProjectDatabase::instance()->groups[] = $manager;
