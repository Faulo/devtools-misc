<?php
namespace Slothsoft\Devtools\Misc\Update\Server;

use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class ApacheSymlink implements UpdateInterface {

    const VHOSTS = '/Webserver/htdocs/vhosts';

    public function runOn(Project $project) {
        if ($vhosts = realpath(self::VHOSTS)) {
            $vhost = $vhosts . DIRECTORY_SEPARATOR . $project->info['name'];
            if (! file_exists($vhost)) {
                $command = sprintf('mklink /d %s %s', escapeshellarg($vhost), escapeshellarg($project->workspace));
                passthru($command);
            }
        }
    }
}

