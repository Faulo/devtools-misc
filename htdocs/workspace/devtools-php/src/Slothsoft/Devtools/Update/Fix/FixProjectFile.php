<?php
namespace Slothsoft\Devtools\Update\Fix;

use Slothsoft\Devtools\Composer\ComposerManifest;
use Slothsoft\Devtools\Update\UpdateInterface;

class FixProjectFile implements UpdateInterface {

    public function runOn(array $project) {
        $doc = new \DOMDocument();
        $doc->load($project['projectFile']);
        assert($doc->documentElement);

        $composer = new ComposerManifest($project['composerFile']);
        $composer->load();

        $projectNode = $doc->getElementsByTagName('projects')->item(0);
        assert($projectNode);

        while ($projectNode->hasChildNodes()) {
            $projectNode->removeChild($projectNode->lastChild);
        }
        foreach ($composer->getRelatedProjects() as $entry) {
            $node = $doc->createElement('project');
            $node->textContent = $entry;
            $projectNode->appendChild($node);
        }

        while ($node = $doc->getElementsByTagName('linkedResources')->item(0)) {
            $node->parentNode->removeChild($node);
        }

        if ($doc->getElementsByTagName('linkedResources')->length === 0) {
            $xml = <<<EOT
<linkedResources>
    <link>
        <name>vendor/slothsoft</name>
        <type>2</type>
        <locationURI>virtual:/virtual</locationURI>
    </link>
</linkedResources>
EOT;
            $fragment = $doc->createDocumentFragment();
            $fragment->appendXML($xml);
            $doc->documentElement->appendChild($fragment);
        }

        $doc->save($project['projectFile']);
    }
}

