<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Unity;

use Slothsoft\Devtools\Misc\Update\UpdateFactory;

class UnityUpdateFactory extends UpdateFactory {

    public function __construct() {
        $this->updates['tests'] = new RunTests();
    }
}

