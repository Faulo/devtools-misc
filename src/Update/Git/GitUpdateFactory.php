<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Git;

use Slothsoft\Devtools\Misc\Update\UpdateFactory;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class GitUpdateFactory extends UpdateFactory {

    public function createUpdate(string $id): ?UpdateInterface {
        switch ($id) {
            case 'pull':
                return new Pull();
            case 'reset':
                return new Reset();
            case 'commit':
                return new Commit('build: update files');
            case 'push':
                return new Push();
            case 'flow-init':
                return new FlowInit();
            case 'flow-patch':
                return new FlowPatch();
        }

        return null;
    }
}

