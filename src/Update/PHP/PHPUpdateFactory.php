<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\PHP;

use Slothsoft\Devtools\Misc\Update\UpdateFactory;

class PHPUpdateFactory extends UpdateFactory {

    public function __construct() {
        $this->updates['tests'] = new RunTests();
        $this->updates['docs'] = new DocsCreate();
    }
}

