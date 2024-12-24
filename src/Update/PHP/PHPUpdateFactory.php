<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\PHP;

use Slothsoft\Devtools\Misc\Update\UpdateFactory;
use Slothsoft\Devtools\Misc\Update\Eclipse\FixXmlCatalog;

class PHPUpdateFactory extends UpdateFactory {

    public function __construct() {
        $this->updates['tests'] = new RunTests();
        $this->updates['docs'] = new DocsCreate();
        $this->updates['eclipse-catalog'] = new FixXmlCatalog();
    }
}

