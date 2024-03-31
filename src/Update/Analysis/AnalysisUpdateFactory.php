<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Analysis;

use Slothsoft\Devtools\Misc\Update\UpdateFactory;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class AnalysisUpdateFactory extends UpdateFactory {

    public function createUpdate(string $id): ?UpdateInterface {
        switch ($id) {
            case 'echo':
                return new EchoProject();
        }

        return null;
    }
}

