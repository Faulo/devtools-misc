<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update;

class GitUpdateFactory extends UpdateFactory {

    public function createUpdate(string $id): ?UpdateInterface {
        switch ($id) {
            case 'git-pull':
                return new Git\Pull();
        }

        return null;
    }
}

