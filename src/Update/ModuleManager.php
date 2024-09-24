<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update;

class ModuleManager extends PHPProjectManager {

    protected function loadProject(array &$project): void {
        parent::loadProject($project);

        $project['farahId'] ??= "farah://$project[vendor]@$project[name]";
        $project['repository'] ??= "https://github.com/Faulo/$project[vendor]-$project[name]";
        $project['homeUrl'] ??= "http://farah.slothsoft.net/modules/$project[name]";
        $project['packagistUrl'] ??= "https://packagist.org/packages/$project[composerId]";
        $project['workspaceId'] ??= "$project[vendor]-$project[name]";
        $project['id'] ??= $project['composerId'];
    }
}

