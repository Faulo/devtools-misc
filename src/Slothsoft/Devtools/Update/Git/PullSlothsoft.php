<?php
namespace Slothsoft\Devtools\Update\Git;

use Slothsoft\Devtools\Update\UpdateInterface;

class PullSlothsoft implements UpdateInterface {

    public function runOn(array $project) {
        $vendorDir = $project['workspaceDir'] . 'vendor/slothsoft/';
        foreach (array_diff(scandir($vendorDir), [
            '.',
            '..'
        ]) as $module) {
            chdir($vendorDir . $module);
            passthru('git pull');
        }
    }
}

