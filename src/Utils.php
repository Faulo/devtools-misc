<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc;

use Slothsoft\Core\FileSystem;
use Symfony\Component\Process\Process;

class Utils {

    public static function ensurePath(string $base, string ...$create): string {
        $root = realpath($base) or die("Failed to find base path '$base'");
        foreach ($create as $dir) {
            $root .= DIRECTORY_SEPARATOR . $dir;
            if (! realpath($root)) {
                mkdir($root);
            }
            $root = realpath($root) or die("Failed to find create directory '$dir' in  '$base'");
        }
        return $root;
    }

    public static function readJson(string $path): array {
        echo PHP_EOL . '> read json ' . escapeshellarg($path) . PHP_EOL;
        return json_decode(file_get_contents($path), true);
    }

    public static function writeJson(string $path, array $data, int $tabLength = 2, $eot = ''): void {
        echo PHP_EOL . '> write json ' . escapeshellarg($path) . PHP_EOL;

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        if ($tabLength !== 4) {
            $json = str_replace('    ', str_pad('', $tabLength, ' '), $json);
        }

        $json .= $eot;

        file_put_contents($path, $json);
    }

    public static function copyDirectory(string $source, string $target): void {
        $command = sprintf('xcopy %s %s /c /e /i /h /r /k /y', escapeshellarg($source), escapeshellarg($target));
        self::execute($command);
    }

    public static function normalize(string $id): string {
        $id = strtolower($id);
        $id = preg_replace('~\s+~', ' ', $id);
        return trim($id);
    }

    public static function toId(string $name): string {
        return preg_replace('~\s+~', '', self::normalize($name));
    }

    public static function toUrl(string $name): string {
        $name = str_replace('_', '_5f', $name);
        $name = str_replace('.', '_2e', $name);
        return $name;
    }

    public static function tokenize(string $tokens): array {
        return explode(' ', self::normalize($tokens));
    }

    public static function execute(string $command): int {
        echo PHP_EOL . '> ' . $command . PHP_EOL;
        $process = Process::fromShellCommandline($command);
        $process->setTimeout(24 * 3600);
        $process->setIdleTimeout(3600);
        $process->start();

        foreach ($process as $type => $data) {
            if ($type === $process::OUT) {
                fwrite(STDOUT, $data);
            } else {
                fwrite(STDERR, $data);
            }
        }

        return $process->getExitCode();
    }

    public static function delete(string $file): void {
        if ($file = realpath($file)) {
            $name = substr($file, strlen(realpath(getcwd())) + 1);
            echo "Deleting: $name" . PHP_EOL;

            if (is_dir($file)) {
                FileSystem::removeDir($file);
            } else {
                unlink($file);
            }
        }
    }

    public static function getAllDirectories(string $root): iterable {
        if ($root = realpath($root)) {
            foreach (glob($root . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR | GLOB_NOSORT) as $directory) {
                yield $directory;
                yield from self::getAllDirectories($directory);
            }
        }
    }
}