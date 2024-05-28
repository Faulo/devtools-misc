<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Unity;

class ReferenceSorter {

    public static function sortPackages(array &$packages, $modulesFirst = false): bool {
        $sortedDependencies = $packages;
        ksort($sortedDependencies);
        $dependencies = [];
        $modules = [];
        foreach ($sortedDependencies as $key => $val) {
            if (strpos($key, 'com.unity.modules.') === 0) {
                $modules[$key] = $val;
            } else {
                $dependencies[$key] = $val;
            }
        }
        $sortedDependencies = $modulesFirst ? $modules + $dependencies : $dependencies + $modules;

        if ($packages !== $sortedDependencies) {
            $packages = $sortedDependencies;
            return true;
        }

        return false;
    }

    public static function sortAssemblies(array &$assemblies): bool {
        $sortedDependencies = $assemblies;
        sort($sortedDependencies);
        $dependencies = [];
        $modules = [];
        foreach ($sortedDependencies as $key) {
            if (strpos($key, 'Unity') === 0) {
                $modules[] = $key;
            } else {
                $dependencies[] = $key;
            }
        }
        $sortedDependencies = array_merge($modules, $dependencies);

        if ($assemblies !== $sortedDependencies) {
            $assemblies = $sortedDependencies;
            return true;
        }

        return false;
    }
}