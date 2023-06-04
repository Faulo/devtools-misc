<?php
namespace Slothsoft\Devtools;

class ModuleManager extends ProjectManager {

    public static function createSlothsoftModules(...$names): array {
        $modules = [];
        foreach ($names as $name) {
            $modules[] = [
                'vendor' => 'slothsoft',
                'name' => $name,
                'composerId' => "slothsoft/$name",
                'farahId' => "farah://slothsoft@$name",
                'githubUrl' => "https://github.com/Faulo/slothsoft-$name",
                'homeUrl' => "http://farah.slothsoft.net/modules/$name",
                'packagistUrl' => "https://packagist.org/packages/slothsoft/$name"
            ];
        }
        return $modules;
    }

    protected function loadProject(array &$module) {
        $module['workspaceId'] = "$module[vendor]-$module[name]";
    }
}

