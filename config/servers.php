<?php
declare(strict_types = 1);

use Slothsoft\Devtools\Misc\Update\ProjectDatabase;
use Slothsoft\Devtools\Misc\Update\ServerManager;

$workspace = realpath('/PHP');

$servers = [
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
        'name' => 'daniel-schulz.slothsoft.net',
        'repository' => 'https://github.com/Faulo/server-daniel-schulz.slothsoft.net',
        'homeUrl' => 'http://daniel-schulz.slothsoft.net'
    ],
    [
        'name' => 'dev.slothsoft.net',
        'repository' => 'https://github.com/Faulo/server-dev.slothsoft.net',
        'homeUrl' => 'http://dev.slothsoft.net'
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

ProjectDatabase::instance()->groups[] = new ServerManager('server', $workspace, $servers);
