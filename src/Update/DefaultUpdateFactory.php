<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update;

class DefaultUpdateFactory extends UpdateFactory {

    public function createUpdate(string $id): ?UpdateInterface {
        switch ($id) {
            case 'echo':
                return new Misc\EchoProject();
        }

        return null;
    }
}

