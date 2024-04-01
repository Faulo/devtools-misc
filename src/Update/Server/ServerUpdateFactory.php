<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Server;

use Slothsoft\Devtools\Misc\Update\UpdateFactory;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class ServerUpdateFactory extends UpdateFactory {

    public function createUpdate(string $id): ?UpdateInterface {
        switch ($id) {
            case 'apache-symlink':
                return new ApacheSymlink();
            case 'apache-conf':
                return new ApacheConf();
            case 'jenkinsfile':
                return new Jenkinsfile();
        }

        return null;
    }
}

