<?php
namespace Slothsoft\Devtools\Misc;

use Symfony\Component\Process\Process;

class CLI {

    public static function normalize(string $id): string {
        $id = strtolower($id);
        $id = preg_replace('~\s+~', ' ', $id);
        return $id;
    }

    public static function toId(string $name): string {
        return preg_replace('~\s+~', '', self::normalize($name));
    }

    public static function tokenize(string $tokens): array {
        return explode(' ', self::normalize($tokens));
    }

    public static function execute(string $command): int {
        echo PHP_EOL . PHP_EOL . '> ' . $command . PHP_EOL;
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
}