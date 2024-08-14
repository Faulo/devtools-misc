<?php
declare(strict_types = 1);

use Slothsoft\Devtools\Misc\Update\ProjectDatabase;
use Slothsoft\Devtools\Misc\Update\ProjectManager;

$workspace = realpath('/PHP');

$projects = [
    [
        'name' => 'devtools-misc',
        'repository' => 'https://github.com/Faulo/devtools-php'
    ]
];

$devops = new ProjectManager('devops', $workspace, 'git');

foreach ($projects as $project) {
    $devops->projects[] = $devops->createProject($project);
}

ProjectDatabase::instance()->groups[] = $devops;