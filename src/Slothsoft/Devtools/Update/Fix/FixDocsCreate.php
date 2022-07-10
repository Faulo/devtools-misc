<?php
namespace Slothsoft\Devtools\Update\Fix;

use Slothsoft\Core\CLI;
use Slothsoft\Devtools\Update\UpdateInterface;

class FixDocsCreate implements UpdateInterface {

    public function runOn(array $project) {
        $version = 'latest';
        // $version = '2.9.0';
        // $version = '3.0.0-alpha1';
        if (is_file($project['workspaceDir'] . 'phpdoc.dist.xml')) {
            CLI::execute('phpDocumentor run');
        }
    }
}

