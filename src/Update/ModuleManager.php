<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update;

class ModuleManager extends PHPProjectManager {

    protected function loadProject(array &$project): void {
        $project['vendor'] ??= 'slothsoft';
        $project['composerId'] ??= "$project[vendor]/$project[name]";
        $project['workspaceId'] ??= "$project[vendor]-$project[name]";

        parent::loadProject($project);

        $project['farahId'] ??= "farah://$project[vendor]@$project[name]";
        $project['repository'] ??= "https://github.com/Faulo/$project[vendor]-$project[name]";
        $project['homeUrl'] ??= "http://farah.slothsoft.net/modules/$project[name]";
        $project['packagistUrl'] ??= "https://packagist.org/packages/$project[composerId]";
        $project['id'] ??= $project['composerId'];
    }
}

