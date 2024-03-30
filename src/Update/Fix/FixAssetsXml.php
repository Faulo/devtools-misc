<?php
namespace Slothsoft\Devtools\Misc\Update\Fix;

use Slothsoft\Devtools\Misc\Update\UpdateInterface;
use DOMElement;
use Slothsoft\Core\DOMHelper;

class FixAssetsXml implements UpdateInterface {

    public function runOn(array $project) {
        if (! is_dir($project['assetsDir'])) {
            // mkdir($project['assetsDir']);
        }
        if (is_file($project['oldAssetsFile']) and ! is_file($project['assetsFile'])) {
            // rename($project['oldAssetsFile'], $project['assetsFile']);
        }

        if (is_file($project['assetsFile'])) {
            $doc = DOMHelper::loadDocument($project['assetsFile']);
            $xpath = DOMHelper::loadXPath($doc, DOMHelper::XPATH_SLOTHSOFT);

            $changes = 0;
            // $changes += $this->fixReferences(...$xpath->evaluate('//*[@ref]'));
            // $changes += $this->fixNames(...$xpath->evaluate('//*[not(@name)][not(self::sfm:import)]'));
            // $changes += $this->fixImports(...$xpath->evaluate('//sfm:import[@name]'));
            // $changes += $this->fixAliases(...$xpath->evaluate('//*[@as]'));
            $changes += $this->removeNames(...$xpath->evaluate('//sfm:import | //sfm:use-template | //sfm:link-stylesheet | //sfm:link-script | //sfm:link-module'));

            if ($changes > 0) {
                echo "Made $changes changes to $project[assetsFile]" . PHP_EOL;
                $doc->save($project['assetsFile']);
            }
        }
    }

    private function fixReferences(DOMElement ...$nodes): int {
        $changed = 0;
        foreach ($nodes as $node) {
            $ref = $node->getAttribute('ref');
            if (substr($ref, 0, 1) === '/') {
                // echo " $ref". PHP_EOL;
            } else {
                echo "  '$ref'" . PHP_EOL;
                $node->setAttribute('ref', "/$ref");
                $changed ++;
            }
        }
        return $changed;
    }

    private function fixNames(DOMElement ...$nodes): int {
        $changed = 0;
        foreach ($nodes as $node) {
            if ($node->hasAttribute('as')) {
                $node->setAttribute('name', $node->getAttribute('as'));
                $changed ++;
            } elseif ($node->hasAttribute('ref')) {
                $node->setAttribute('name', basename(parse_url($node->getAttribute('ref'), PHP_URL_PATH)));
                $changed ++;
            }
        }
        return $changed;
    }

    private function removeNames(DOMElement ...$nodes): int {
        $changed = 0;
        foreach ($nodes as $node) {
            if ($node->hasAttribute('name')) {
                $node->removeAttribute('name');
                $changed ++;
            }
        }
        return $changed;
    }

    private function fixImports(DOMElement ...$nodes): int {
        $changed = 0;
        foreach ($nodes as $node) {
            $node->removeAttribute('name');
            $changed ++;
        }
        return $changed;
    }

    private function fixAliases(DOMElement ...$nodes): int {
        $changed = 0;
        foreach ($nodes as $node) {
            $node->setAttribute('name', $node->getAttribute('as'));
            $node->removeAttribute('as');
            $changed ++;
        }
        return $changed;
    }
}

