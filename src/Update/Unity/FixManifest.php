<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Unity;

use Slothsoft\Devtools\Misc\Utils;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;
use Slothsoft\Unity\UnityProjectInfo;

class FixManifest implements UpdateInterface {

    private array $scopedRegistries;

    private array $requiredDependencies = [];

    private array $optionalDependencies = [];

    private array $forbiddenDependencies = [];

    public bool $alwaysSave = false;

    public function __construct(array $scopedRegistries) {
        $this->scopedRegistries = $scopedRegistries;
    }

    public function setRequiredDependencies(array $requiredDependencies): void {
        $this->requiredDependencies = $requiredDependencies;
    }

    public function setOptionalDependencies(array $optionalDependencies): void {
        $this->optionalDependencies = $optionalDependencies;
    }

    public function setForbiddenDependencies(array $forbiddenDependencies): void {
        $this->forbiddenDependencies = $forbiddenDependencies;
    }

    public function runOn(Project $project) {
        if ($project->chdir()) {
            if ($unity = UnityProjectInfo::find('.', true)) {
                if ($manifestPath = realpath($unity->path . '/Packages/manifest.json')) {
                    $manifest = Utils::readJson($manifestPath);

                    $hasChanged = $this->alwaysSave;

                    if (! isset($manifest['dependencies'])) {
                        $manifest['dependencies'] = [];
                        $hasChanged = true;
                    }

                    foreach ($this->requiredDependencies as $key => $val) {
                        if (! isset($manifest['dependencies'][$key]) or $manifest['dependencies'][$key] !== $val) {
                            $manifest['dependencies'][$key] = $val;
                            $hasChanged = true;
                        }
                    }

                    foreach ($this->optionalDependencies as $key => $val) {
                        if (isset($manifest['dependencies'][$key])) {
                            if (is_array($val)) {
                                unset($manifest['dependencies'][$key]);

                                foreach ($val as $k => $v) {
                                    $manifest['dependencies'][$k] = $v;
                                }

                                $hasChanged = true;
                            } else {
                                if ($manifest['dependencies'][$key] !== $val) {
                                    $manifest['dependencies'][$key] = $val;
                                    $hasChanged = true;
                                }
                            }
                        }
                    }

                    foreach ($this->forbiddenDependencies as $key) {
                        if (isset($manifest['dependencies'][$key])) {
                            unset($manifest['dependencies'][$key]);
                            $hasChanged = true;
                        }
                    }

                    if ($manifest['scopedRegistries'] !== $this->scopedRegistries) {
                        $manifest['scopedRegistries'] = $this->scopedRegistries;
                        $hasChanged = true;
                    }

                    if (ReferenceSorter::sortPackages($manifest['dependencies'])) {
                        $hasChanged = true;
                    }

                    if ($hasChanged) {
                        Utils::writeJson($manifestPath, $manifest, 2, "\n");
                    }
                }
            }
        }
    }
}

