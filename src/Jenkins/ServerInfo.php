<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Jenkins;

use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;

class ServerInfo {

    const FILE_CONFIG = '/config.xml';

    public static function find(string $directory, bool $includeSubdirectories = false): ?ServerInfo {
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
            $iterator = new RecursiveCallbackFilterIterator(new RecursiveDirectoryIterator($directory), function (\SplFileInfo $file, string $path, RecursiveDirectoryIterator $iterator): bool {
                return $file->isDir() and $file->getBasename() !== '..';
            });
            $directories = [];
            foreach ($iterator as $file) {
                $directories[] = $file->getRealPath();
            }
            sort($directories);
            foreach ($directories as $projectDirectory) {
                if ($project = self::create($projectDirectory)) {
                    yield $project;
                }
            }
        }
    }

    private static function create(string $directory): ?ServerInfo {
        if (is_dir($directory) and is_file($directory . self::FILE_CONFIG)) {
            return new ServerInfo($directory);
        }
        return null;
    }

    /** @var string */
    public string $path;

    /** @var array */
    public array $jobs;

    private function __construct(string $path) {
        $this->path = $path;
        $this->jobs = [
            ...JobInfo::findAll($path . DIRECTORY_SEPARATOR . 'jobs')
        ];
    }
}