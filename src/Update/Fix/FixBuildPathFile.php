<?php
namespace Slothsoft\Devtools\Misc\Update\Fix;

use Slothsoft\Devtools\Misc\Update\UpdateInterface;
use Slothsoft\Devtools\Misc\Composer\ComposerManifest;

class FixBuildPathFile implements UpdateInterface {

    public function runOn(array $project) {
        $doc = new \DOMDocument();
        $doc->load($project['buildpathFile']);
        assert($doc->documentElement);

        $composer = new ComposerManifest($project['composerFile']);
        $composer->load();

        $buildpathNodes = [];
        foreach ($doc->getElementsByTagName('buildpathentry') as $node) {
            $buildpathNodes[] = $node;
        }

        foreach ($buildpathNodes as $node) {
            $path = $node->getAttribute('path');
            if (strpos($path, 'slothsoft') !== false) {
                $node->parentNode->removeChild($node);
            }
        }
        foreach ($composer->getRelatedProjects() as $entry) {
            $node = $doc->createElement('buildpathentry');
            $node->setAttribute("combineaccessrules", "false");
            $node->setAttribute("kind", "prj");
            $node->setAttribute("path", $entry);
            $doc->documentElement->appendChild($node);
        }

        $doc->save($project['buildpathFile']);
    }
}

