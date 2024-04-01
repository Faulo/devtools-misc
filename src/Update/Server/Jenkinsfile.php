<?php
namespace Slothsoft\Devtools\Misc\Update\Server;

use Slothsoft\Devtools\Misc\Update\PHPExecutor;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class Jenkinsfile implements UpdateInterface {

    const CONF_NAME = 'Jenkinsfile';

    const CONF_CONTENT = <<<EOT
    farahServerPipeline {
        VHOST_NAME = '%1\$s'
        PHP_VERSION = '%2\$s'
    }
    
    EOT;

    public function runOn(Project $project) {
        if ($project->chdir()) {
            $php = new PHPExecutor();
            $content = vsprintf(self::CONF_CONTENT, [
                $project->name,
                $php->version
            ]);
            file_put_contents(self::CONF_NAME, $content);
        }
    }
}

