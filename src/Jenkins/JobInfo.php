<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Jenkins;

use Slothsoft\Core\DOMHelper;
use DOMDocument;
use DOMXPath;
use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class JobInfo {

    const FILE_CONFIG = '/config.xml';

    public static function find(string $directory, bool $includeSubdirectories = false): ?JobInfo {
        if ($includeSubdirectories) {
            foreach (self::findAll($directory) as $info) {
                return $info;
            }
            return null;
        } else {
            return self::create($directory);
        }
    }

    public static function findAll(string $directory): iterable {
        if (is_dir($directory)) {
            $iterator = new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS);
            $iterator = new RecursiveCallbackFilterIterator($iterator, function (\SplFileInfo $file, string $path, RecursiveDirectoryIterator $iterator): bool {
                return $file->isDir();
            });
            $iterator = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);
            $directories = [];
            foreach ($iterator as $file) {
                if ($file->isDir()) {
                    $directories[] = $file->getRealPath();
                }
            }
            sort($directories);
            foreach ($directories as $projectDirectory) {
                if ($project = self::create($projectDirectory)) {
                    yield $project;
                }
            }
        }
    }

    private static function create(string $directory): ?JobInfo {
        if (is_dir($directory) and is_file($directory . self::FILE_CONFIG)) {
            @$document = DOMHelper::loadDocument($directory . self::FILE_CONFIG);
            if ($document and $document->documentElement) {
                return new JobInfo($directory, $document);
            }
        }
        return null;
    }

    /** @var string */
    public string $path;

    public DOMDocument $config;

    public DOMXPath $xpath;

    private function __construct(string $path, DOMDocument $config) {
        $this->path = $path;
        $this->config = $config;
        $this->xpath = DOMHelper::loadXPath($config);
    }

    public function isFlowJob(): bool {
        return $this->config->documentElement->tagName === 'flow-definition';
    }

    public function clearActions(): bool {
        foreach ($this->config->getElementsByTagName('actions') as $node) {
            if ($node->firstChild) {
                while ($node->firstChild) {
                    $node->removeChild($node->firstChild);
                }

                return true;
            }
        }
        return false;
    }

    public function setProperties(array $properties): bool {
        $hasChanged = false;

        $parent = $this->xpath->evaluate('/*/properties')->item(0);
        if ($parent) {
            foreach ($properties as $name => $shouldExist) {
                $node = $parent->getElementsByTagName($name)->item(0);
                $doesExist = (bool) $node;

                if ($shouldExist !== $doesExist) {
                    $hasChanged = true;
                    if ($shouldExist) {
                        $parent->appendChild($this->config->createElement($name));
                    } else {
                        $node->parentNode->removeChild($node);
                    }
                }
            }
        }

        return $hasChanged;
    }

    public function setElements(array $elements): bool {
        $hasChanged = false;

        foreach ($elements as $query => $value) {
            foreach ($this->xpath->evaluate($query) as $node) {
                if ($node->textContent != $value) {
                    $node->textContent = $value;
                    $hasChanged = true;
                }
            }
        }

        return $hasChanged;
    }

    public function save() {
        $this->config->save($this->path . self::FILE_CONFIG);
    }
}