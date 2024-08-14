<?php
declare(strict_types = 1);

use Slothsoft\Devtools\Misc\Update\ModuleManager;
use Slothsoft\Devtools\Misc\Update\ProjectDatabase;

$workspace = realpath('/PHP');

$modules = [
    [
        'name' => 'amber'
    ],
    [
        'name' => 'blob'
    ],
    [
        'name' => 'chat'
    ],
    [
        'name' => 'comics'
    ],
    [
        'name' => 'core'
    ],
    [
        'name' => 'cron'
    ],
    [
        'name' => 'dragonage'
    ],
    [
        'name' => 'farah'
    ],
    [
        'name' => 'fireemblem'
    ],
    [
        'name' => 'lang'
    ],
    [
        'name' => 'minecraft'
    ],
    [
        'name' => 'mtg'
    ],
    [
        'name' => 'pokemon'
    ],
    [
        'name' => 'talesof'
    ],
    [
        'name' => 'savegame'
    ],
    [
        'name' => 'schema'
    ],
    [
        'name' => 'sse'
    ],
    [
        'name' => 'tetris'
    ],
    [
        'name' => 'therapy'
    ],
    [
        'name' => 'twitter'
    ],
    [
        'name' => 'unity'
    ],
    [
        'name' => 'w3c'
    ],
    [
        'name' => 'webrtc'
    ],
    [
        'name' => 'whatthehell'
    ]
];

ProjectDatabase::instance()->groups[] = new ModuleManager('module', $workspace, $modules);