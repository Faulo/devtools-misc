<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Unity;

use Slothsoft\Devtools\Misc\Update\UpdateFactory;

class UnityUpdateFactory extends UpdateFactory {

    public function __construct() {
        $this->updates['build'] = new BuildSolution();
        $this->updates['tests'] = new RunTests();
        $this->updates['format'] = new FormatCode();
        $this->updates['delete-empty'] = new DeleteEmptyFolders();
        $this->updates['fill-empty'] = new FillEmptyFolders();
    }
}

