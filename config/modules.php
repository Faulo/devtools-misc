<?php
declare(strict_types = 1);

use Slothsoft\Devtools\Misc\Update\Group;
use Slothsoft\Devtools\Misc\Update\ModuleManager;
use Slothsoft\Devtools\Misc\Update\ProjectDatabase;
use Slothsoft\Devtools\Misc\Update\StaticFolder\StaticDeleteUpdate;
use Slothsoft\Devtools\Misc\Update\StaticFolder\StaticFolderFactory;

$workspace = realpath('/PHP');
if (! $workspace) {
    return;
}

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

$manager = new ModuleManager('module', $workspace);

$manager->addGroup('module', $modules);

$staticUpdates = new StaticFolderFactory();
$staticUpdates->addCopy('copy-devops', 'static/slothsoft/devops.module', true, true);
$staticUpdates->addUpdate('delete-devops', new StaticDeleteUpdate('composer.phar', 'phpdoc.dist.xml'));
$manager->updateFactories[] = $staticUpdates;

ProjectDatabase::instance()->groups[] = $manager;

$eclipse = new Group('eclipse');
$eclipse->groups[] = $manager;
ProjectDatabase::instance()->groups[] = $eclipse;