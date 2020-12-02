<?php
namespace Slothsoft\Devtools;

class ModuleManager extends ProjectManager {

    protected function loadProject(array &$module) {
        $module['workspaceId'] = "$module[vendor]-$module[name]";
    }
}

