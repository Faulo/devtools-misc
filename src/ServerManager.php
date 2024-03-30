<?php
namespace Slothsoft\Devtools\Misc;

class ServerManager extends ProjectManager {

    protected function loadProject(array &$server) {
        $server['vendor'] = 'slothsoft';
        $server['workspaceId'] = "server-$server[name]";
        $server['composerId'] = "slothsoft/$server[name]";
    }
}

