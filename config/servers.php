<?php
declare(strict_types = 1);

use Slothsoft\Devtools\Misc\ProjectDatabase;
use Slothsoft\Devtools\Misc\ServerManager;

$servers = [
    [
        'name' => 'daniel-schulz.slothsoft.net',
        'githubUrl' => 'https://github.com/Faulo/server-daniel-schulz.slothsoft.net',
        'homeUrl' => 'http://daniel-schulz.slothsoft.net'
    ],
    [
        'name' => 'dev.slothsoft.net',
        'githubUrl' => 'https://github.com/Faulo/server-dev.slothsoft.net',
        'homeUrl' => 'http://dev.slothsoft.net'
    ],
    [
        'name' => 'farah.slothsoft.net',
        'githubUrl' => 'https://github.com/Faulo/server-farah.slothsoft.net',
        'homeUrl' => 'http://farah.slothsoft.net'
    ],
    [
        'name' => 'mtg.slothsoft.net',
        'githubUrl' => 'https://github.com/Faulo/server-mtg.slothsoft.net',
        'homeUrl' => 'http://mtg.slothsoft.net'
    ],
    [
        'name' => 'schema.slothsoft.net',
        'githubUrl' => 'https://github.com/Faulo/server-schema.slothsoft.net',
        'homeUrl' => 'http://schema.slothsoft.net'
    ],
    [
        'name' => 'slothsoft.net',
        'githubUrl' => 'https://github.com/Faulo/server-slothsoft.net',
        'homeUrl' => 'http://slothsoft.net'
    ],
    [
        'name' => 'amber.slothsoft.net',
        'githubUrl' => 'https://github.com/Faulo/server-amber.slothsoft.net',
        'homeUrl' => 'http://amber.slothsoft.net'
    ]
];

ProjectDatabase::instance()->groups[] = new ServerManager('server', 'R:\\Eclipse\\workspace', $servers);
