<?php
declare(strict_types = 1);

use Slothsoft\Devtools\Misc\Update\ProjectDatabase;
use Slothsoft\Devtools\Misc\Update\ProjectManager;

$workspace = realpath('/PHP');

$projects = [
    [
        'name' => 'devtools-misc',
        'repository' => 'https://github.com/Faulo/devtools-php'
    ],
    [
        'name' => 'Slothsoft.Docker.Jenkins',
        'repository' => 'https://github.com/Faulo/docker-jenkins'
    ],
    [
        'name' => 'Slothsoft.Docker.Valheim',
        'repository' => 'https://github.com/Faulo/docker-valheim'
    ],
    [
        'name' => 'Slothsoft.Jenkins.Slothsoft',
        'repository' => 'https://github.com/Faulo/jenkins-slothsoft'
    ],
    [
        'name' => 'Slothsoft.Jenkins.Unity',
        'repository' => 'https://github.com/Faulo/jenkins-unity'
    ]
];

$devops = new ProjectManager('devops', $workspace, 'git');

foreach ($projects as $project) {
    $devops->projects[] = $devops->createProject($project);
}

ProjectDatabase::instance()->groups[] = $devops;