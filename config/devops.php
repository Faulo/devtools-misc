<?php
declare(strict_types = 1);

use Slothsoft\Devtools\Misc\Update\PHPProjectManager;
use Slothsoft\Devtools\Misc\Update\ProjectDatabase;
use Slothsoft\Devtools\Misc\Update\ProjectManager;
use Slothsoft\Devtools\Misc\Update\Group;

$workspace = realpath('/PHP');
if (! $workspace) {
    return;
}

$phps = [
    [
        'name' => 'devtools-misc',
        'repository' => 'https://github.com/Faulo/devtools-php'
    ],
    [
        'name' => 'devtools-unity-pp',
        'repository' => 'https://github.com/Faulo/devtools-unity-pp'
    ]
];

$projects = [
    [
        'name' => 'docker-farah',
        'repository' => 'https://github.com/Faulo/docker-farah'
    ],
    [
        'name' => 'docker-jenkins',
        'repository' => 'https://github.com/Faulo/docker-jenkins'
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

$group = new Group('devops');

$manager = new PHPProjectManager('devops.php', $workspace, $phps);

$group->groups[] = $manager;

$manager = new ProjectManager('devops.jenkins', $workspace, 'git');

foreach ($projects as $project) {
    $manager->projects[] = $manager->createProject($project);
}

$group->groups[] = $manager;

ProjectDatabase::instance()->groups[] = $group;