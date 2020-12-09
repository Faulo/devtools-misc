<?php
namespace Slothsoft\Devtools\Update\Fix;

use Slothsoft\Devtools\Update\UpdateInterface;

class FixIncludePathFile implements UpdateInterface {

    public function runOn(array $project) {
        $doc = new \DOMDocument();
        $doc->load($project['buildpathFile']);
        assert($doc->documentElement);

        $buildpathEntries = [];
        $buildpathEntries[] = 'org.eclipse.dltk.USER_LIBRARY/COM';
        $buildpathEntries[] = 'org.eclipse.dltk.USER_LIBRARY/Pthreads';

        $composerData = json_decode(file_get_contents($project['composerFile']), true);
        assert($composerData);
        foreach (array_keys($composerData['require']) as $module) {
            if (strpos($module, 'slothsoft/') === 0) {
                $buildpathEntries[] = '/' . str_replace('/', '-', $module);
            }
        }

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
        foreach ($buildpathEntries as $entry) {
            $node = $doc->createElement('buildpathentry');
            $node->setAttribute("combineaccessrules", "false");
            $node->setAttribute("kind", "prj");
            $node->setAttribute("path", $entry);
            $doc->documentElement->appendChild($node);
        }

        $doc->save($project['buildpathFile']);
    }
}

