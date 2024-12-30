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

        foreach (self::getDescendants($asset) as $child) {
            try {
                $path = $child->getFileInfo()->getRealPath();
                if (! $path) {
                    continue;
                }

                $mime = $child->lookupExecutable()
                    ->lookupDefaultResult()
                    ->lookupMimeType();
                if ($mime !== 'application/xslt+xml') {
                    continue;
                }

                $path = 'file:///' . str_replace('\\', '/', $path);

                $url = (string) $child->createUrl();

                $urls[$url] = $path;
            } catch (\Throwable $e) {}
        }

        $catalog = DOMHelper::loadDocument(self::CATALOG_PATH);

        $nodes = [];
        foreach ($catalog->getElementsByTagName('uri') as $node) {
            $nodes[$node->getAttribute('name')] = $node;
        }

        $hasChanged = false;
        foreach ($urls as $url => $path) {
            if (isset($nodes[$url])) {
                if ($nodes[$url]->getAttribute('uri') !== $path) {
                    $nodes[$url]->setAttribute('uri', $path);
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

