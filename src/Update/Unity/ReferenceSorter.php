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

        natcasesort($sortedDependencies);

        $engine = [];
        $unity = [];
        $editor = [];
        $dependencies = [];
        foreach ($sortedDependencies as $key) {
            if (strpos($key, 'UnityEngine') === 0) {
                $engine[] = $key;
                continue;
            }

            if (strpos($key, 'UnityEditor') === 0) {
                $editor[] = $key;
                continue;
            }

            if (strpos($key, 'Unity') === 0) {
                $unity[] = $key;
                continue;
            }

            $dependencies[] = $key;
        }

        $sortedDependencies = array_merge($engine, $editor, $unity, $dependencies);

        if ($assemblies !== $sortedDependencies) {
            $assemblies = $sortedDependencies;
            return true;
        }

        return false;
    }
}