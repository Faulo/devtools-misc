<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Server;

use Slothsoft\Devtools\Misc\Update\UpdateFactory;

class ServerUpdateFactory extends UpdateFactory {

    public function __construct() {
        $this->updates['apache-symlink'] = new ApacheSymlink();
        $this->updates['apache-conf'] = new ApacheConf();
        $this->updates['jenkinsfile'] = new Jenkinsfile();
    }
}

