<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Composer;

use Slothsoft\Devtools\Misc\Update\UpdateFactory;

class ComposerUpdateFactory extends UpdateFactory {

    public function __construct() {
        $this->updates['update'] = new UpdateUsingCLI();
        $this->updates['dump-autoloader'] = new DumpAutoloader();
    }
}

