<?php
namespace Slothsoft\Devtools\Misc\Update\Server;

use Slothsoft\Devtools\Misc\Update\PHPExecutor;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class ApacheConf implements UpdateInterface {

    const CONF_NAME = 'apache.conf';

    const CONF_CONTENT = <<<EOT
    #Listen 80
    <VirtualHost *:80>
        ServerName %1\$s
    
        Include "%2\$s"
    
        DocumentRoot "%3\$s"
    
        <Directory "%3\$s">
    		Options Indexes FollowSymLinks ExecCGI
            AllowOverride All
    		Require all granted
        </Directory>
    </VirtualHost>
    EOT;

    public function runOn(Project $project) {
        if ($project->chdir()) {
            $php = new PHPExecutor();

            $serverName = $project->name;
            $include = "conf/php/cgi-fcgi-$php->version.conf";
            $documentRoot = "/Webserver/htdocs/vhosts/$project->name/public";

            $content = vsprintf(self::CONF_CONTENT, [
                $serverName,
                $include,
                $documentRoot
            ]);
            file_put_contents(self::CONF_NAME, $content);
        }
    }
}

