<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Fix;

use Slothsoft\Devtools\Misc\Update\UpdateFactory;

class FixUpdateFactory extends UpdateFactory {

    public function __construct() {
        $this->updates['fix-eclipse'] = new FixEclipse();
        $this->updates['fix-manifest'] = new FixComposerJson();
    }
}

