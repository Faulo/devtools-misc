<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update;

class ServerManager extends PHPProjectManager {

    protected function loadProject(array &$server) {
        $server['vendor'] = 'slothsoft';
        $server['workspaceId'] = "server-$server[name]";
        $server['composerId'] = "slothsoft/$server[name]";
    }
}

