<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Plastic;

use Slothsoft\Devtools\Misc\Update\UpdateFactory;

class PlasticUpdateFactory extends UpdateFactory {

    public function __construct() {
        $this->updates['pull'] = new Pull();
        $this->updates['reset'] = new Reset();
    }
}

