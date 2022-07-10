<?php
namespace Slothsoft\Devtools\Update\Fix;

use Slothsoft\Core\CLI;
use Slothsoft\Devtools\Update\UpdateInterface;

class FixDocsCreate implements UpdateInterface {

    public function runOn(array $project) {
        if (is_file($project['workspaceDir'] . 'phpdoc.dist.xml')) {
            CLI::execute('phpDocumentor run');
        }
    }
}

