<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Unity;

use Slothsoft\Devtools\Misc\Update\UpdateFactory;

class UnityUpdateFactory extends UpdateFactory {

    public function __construct() {
        $this->updates['tests'] = new RunTests();
        $this->updates['format'] = new FormatCode();
    }

    public function withFixManifest(array $scopedRegistries, array $requiredDependencies = [], array $forbiddenDependencies = []): UnityUpdateFactory {
        $this->updates['fix-manifest'] = new FixManifest($scopedRegistries, $requiredDependencies, $forbiddenDependencies);

        return $this;
    }
}

