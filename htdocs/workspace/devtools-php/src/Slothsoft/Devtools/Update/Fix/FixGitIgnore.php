<?php
namespace Slothsoft\Devtools\Update\Fix;

use Slothsoft\Devtools\Update\UpdateInterface;

class FixGitIgnore implements UpdateInterface {

    private function getRequiredEntries(): array {
        return [
            // Composer
            '/composer.phar',
            '/composer.lock',
            '/vendor/',

            // Node
            '/package-lock.json',
            '/node_modules/',

            // ServerEnvironment
            '/data/',
            '/cache/',
            '/log/',

            'tmp/',
            'temp/',
            'unused/',

            '*Kopie*',
            'Thumbs.db',
            '.DS_Store',
            '*.tmp'
        ];
    }

    private function getDeprecatedEntries(): array {
        return [ // '/composer.lock',
        ];
    }

    public function runOn(array $project) {
        $ignoreList = is_file($project['gitignoreFile']) ? file($project['gitignoreFile'], FILE_IGNORE_NEW_LINES) : [];

        foreach ($this->getRequiredEntries() as $entry) {
            $ignoreList[] = $entry;
        }

        $ignoreList = array_unique($ignoreList);

        foreach ($this->getDeprecatedEntries() as $entry) {
            $i = array_search($entry, $ignoreList);
            if ($i !== false) {
                unset($ignoreList[$i]);
            }
        }

        file_put_contents($project['gitignoreFile'], implode(PHP_EOL, $ignoreList));
    }
}

