<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Composer;

use Slothsoft\Devtools\Misc\Update\UpdateFactory;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class ComposerUpdateFactory extends UpdateFactory {

    public function createUpdate(string $id): ?UpdateInterface {
        switch ($id) {
            case 'update':
                return new UpdateUsingCLI();
            case 'dump-autoloader':
                return new DumpAutoloader();
        }

        return null;
    }
}

