<?php
namespace Slothsoft\Devtools\Misc\Update\Eclipse;

use Slothsoft\Core\DOMHelper;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;
use Slothsoft\Farah\FarahUrl\FarahUrl;
use Slothsoft\Farah\Module\Module;
use Slothsoft\Farah\Module\Asset\AssetInterface;

class FixXmlCatalog implements UpdateInterface {

    const CATALOG_PATH = '../.metadata/.plugins/org.eclipse.wst.xml.core/user_catalog.xml';

    public function runOn(Project $project) {
        if (! file_exists(self::CATALOG_PATH)) {
            return;
        }

        if (! $project->chdir()) {
            return;
        }

        if (! isset($project->info['composer']['autoload']['files'])) {
            return;
        }

        if (! isset($project->info['farahId'])) {
            return;
        }

        $url = FarahUrl::createFromReference($project->info['farahId']);
        try {
            $asset = Module::resolveToAsset($url);
        } catch (\Throwable $e) {
            foreach ($project->info['composer']['autoload']['files'] as $file) {
                try {
                    @require_once $file;

                    $asset = Module::resolveToAsset($url);
                } catch (\Throwable $e) {
                    return;
                }
            }
            return;
        }

        $urls = [];
        $systems = [];

        foreach (self::getDescendants($asset) as $child) {
            try {
                $path = $child->getFileInfo()->getRealPath();
                if (! $path) {
                    continue;
                }

                $name = $child->lookupExecutable()
                    ->lookupDefaultResult()
                    ->lookupFileName();
                $extension = pathinfo($name, PATHINFO_EXTENSION);

                $path = 'file:///' . str_replace('\\', '/', $path);

                $url = (string) $child->createUrl();

                switch ($extension) {
                    case 'xsl':
                        $urls[$url] = $path;
                        break;
                    case 'xsd':
                        $systems[$url] = $path;
                        break;
                }
            } catch (\Throwable $e) {}
        }

        $catalog = DOMHelper::loadDocument(self::CATALOG_PATH);

        $uriNodes = [];
        foreach ($catalog->getElementsByTagName('uri') as $node) {
            $uriNodes[$node->getAttribute('name')] = $node;
        }

        $systemNodes = [];
        foreach ($catalog->getElementsByTagName('system') as $node) {
            $systemNodes[$node->getAttribute('systemId')] = $node;
        }

        $hasChanged = false;

        foreach ($urls as $url => $path) {
            if (isset($uriNodes[$url])) {
                if ($uriNodes[$url]->getAttribute('uri') !== $path) {
                    $uriNodes[$url]->setAttribute('uri', $path);
                    $hasChanged = true;
                    echo $url . PHP_EOL . " => $path" . PHP_EOL;
                }
            } else {
                $node = $catalog->createElement('uri');
                $node->setAttribute('name', $url);
                $node->setAttribute('uri', $path);
                $catalog->documentElement->appendChild($node);
                $hasChanged = true;
                echo $url . PHP_EOL . " => $path" . PHP_EOL;
            }
        }

        foreach ($systems as $url => $path) {
            if (isset($systemNodes[$url])) {
                if ($systemNodes[$url]->getAttribute('uri') !== $path) {
                    $systemNodes[$url]->setAttribute('uri', $path);
                    $hasChanged = true;
                    echo $url . PHP_EOL . " => $path" . PHP_EOL;
                }
            } else {
                $node = $catalog->createElement('system');
                $node->setAttribute('systemId', $url);
                $node->setAttribute('uri', $path);
                $catalog->documentElement->appendChild($node);
                $hasChanged = true;
                echo $url . PHP_EOL . " => $path" . PHP_EOL;
            }
        }

        if ($hasChanged) {
            $catalog->preserveWhiteSpace = false;
            $catalog->formatOutput = true;
            $catalog->save(self::CATALOG_PATH);
        }
    }

    static function getDescendants(AssetInterface $asset): iterable {
        yield $asset;
        try {
            foreach ($asset->getAssetChildren() as $child) {
                yield from self::getDescendants($child);
            }
        } catch (\Throwable $e) {}
    }
}

