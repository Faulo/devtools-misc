<?php
namespace Slothsoft\Devtools;


class ServerManager extends ProjectManager
{
    protected function loadProject(array &$server)
    {
        $server['vendor'] = 'slothsoft';
        $server['workspaceId'] = "server-$server[name]";
        $server['composerId'] = "slothsoft/$server[name]";
    }
}

