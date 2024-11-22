<?php
declare(strict_types = 1);

use Slothsoft\Devtools\Misc\Utils;
use Slothsoft\Devtools\Misc\Update\Group;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\ProjectDatabase;
use Slothsoft\Devtools\Misc\Update\ServerManager;
use Slothsoft\Devtools\Misc\Update\PHP\RunScript;
use Slothsoft\Devtools\Misc\Update\StaticFolder\StaticFolderFactory;
use Slothsoft\Devtools\Misc\Update\StaticFolder\StaticDeleteUpdate;

$workspace = Utils::ensurePath(getenv('UserProfile') . '/Desktop', 'Eclipse');

$backend = [
    [
        'name' => 'backend-jenkins',
        'workspaceId' => 'backend-jenkins',
        'repository' => 'https://github.com/Faulo/backend-jenkins'
    ],
    [
        'name' => 'backend-vhosts',
        'workspaceId' => 'backend-vhosts',
        'repository' => 'https://github.com/Faulo/backend-vhosts'
    ],
    [
        'name' => 'backend-agents',
        'workspaceId' => 'backend-agents',
        'repository' => 'https://github.com/Faulo/backend-agents'
    ],
    [
        'name' => 'backend-docker',
        'workspaceId' => 'backend-docker',
        'repository' => 'https://github.com/Faulo/backend-docker'
    ],
    [
        'name' => 'backend-mysql',
        'workspaceId' => 'backend-mysql',
        'repository' => 'https://github.com/Faulo/backend-mysql'
    ]
];

$farah = [
    [
        'name' => 'mtg.slothsoft.net',
        'repository' => 'https://github.com/Faulo/server-mtg.slothsoft.net',
        'homeUrl' => 'http://mtg.slothsoft.net'
    ],
    [
        'name' => 'amber.slothsoft.net',
        'repository' => 'https://github.com/Faulo/server-amber.slothsoft.net',
        'homeUrl' => 'http://amber.slothsoft.net'
    ],
    [
        'name' => 'dev.slothsoft.net',
        'repository' => 'https://github.com/Faulo/server-dev.slothsoft.net',
        'homeUrl' => 'http://dev.slothsoft.net'
    ],
    [
        'name' => 'schema.slothsoft.net',
        'repository' => 'https://github.com/Faulo/server-schema.slothsoft.net',
        'homeUrl' => 'http://schema.slothsoft.net'
    ],
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
    ],
    [
        'name' => 'slothsoft.net',
        'repository' => 'https://github.com/Faulo/server-slothsoft.net',
        'homeUrl' => 'http://slothsoft.net'
    ],
    [
        'name' => 'trialoftwo.slothsoft.net',
        'repository' => 'https://github.com/Faulo/server-trialoftwo.slothsoft.net',
        'homeUrl' => 'http://trialoftwo.slothsoft.net',
        'vendor' => 'oilcatz'
    ],
    [
        'name' => 'valheim.slothsoft.net',
        'repository' => 'https://github.com/Faulo/server-valheim.slothsoft.net',
        'homeUrl' => 'http://valheim.slothsoft.net'
    ]
];

$groups = [
    'backend' => $backend,
    'farah' => $farah
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
        case 'backend':
            return __DIR__ . '/../static/slothsoft/devops.server.backend';
        case 'farah':
            return __DIR__ . '/../static/slothsoft/devops.server.farah';
    }

    return null;
}, true);
$staticUpdates->addUpdate('deploy', new RunScript('server-deploy.bat'));
$staticUpdates->addUpdate('remove', new RunScript('server-remove.bat'));
$staticUpdates->addUpdate('delete-devops', new StaticDeleteUpdate('composer.phar', '.github', 'apache.conf', 'scripts', 'config.php', 'html/index.php', 'phpdoc.dist.xml'));
$manager->updateFactories[] = $staticUpdates;

ProjectDatabase::instance()->groups[] = $manager;

$eclipse = new Group('eclipse');
$eclipse->groups[] = $manager;
ProjectDatabase::instance()->groups[] = $eclipse;