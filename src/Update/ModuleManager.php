<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update;

class ModuleManager extends PHPProjectManager {

    protected function loadProject(array &$module) {
        $module['vendor'] ??= 'slothsoft';
        $module['composerId'] ??= "$module[vendor]/$module[name]";
        $module['farahId'] ??= "farah://$module[vendor]@$module[name]";
        $module['githubUrl'] ??= "https://github.com/Faulo/$module[vendor]-$module[name]";
        $module['homeUrl'] ??= "http://farah.slothsoft.net/modules/$module[name]";
        $module['packagistUrl'] ??= "https://packagist.org/packages/$module[composerId]";
        $module['workspaceId'] ??= "$module[vendor]-$module[name]";
        $module['id'] ??= $module['composerId'];
    }
}

