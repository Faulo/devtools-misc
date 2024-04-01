<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update;

use Slothsoft\Devtools\Misc\Update\Server\ServerUpdateFactory;

class ServerManager extends PHPProjectManager {

    public function __construct(string $id, string $workspaceDir, array $projects) {
        parent::__construct($id, $workspaceDir, $projects);

        $this->updateFactories[] = new ServerUpdateFactory();
    }

    protected function loadProject(array &$server) {
        $server['vendor'] = 'slothsoft';
        $server['workspaceId'] = "server-$server[name]";
        $server['composerId'] = "slothsoft/$server[name]";
    }
}

