<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Unity;

use Slothsoft\Devtools\Misc\Update\UpdateFactory;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class UnityUpdateFactory extends UpdateFactory {

    public function createUpdate(string $id): ?UpdateInterface {
        switch ($id) {
            case 'tests':
                return new RunTests();
        }

        return null;
    }
}

