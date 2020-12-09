<?php
namespace Slothsoft\Devtools;

use Symfony\Component\Process\Process;

class CLI {

    public static function execute(string $command): int {
        echo PHP_EOL . PHP_EOL . '> ' . $command . PHP_EOL;
        $process = Process::fromShellCommandline($command);
        $process->start();

        foreach ($process as $type => $data) {
            if ($type === $process::OUT) {
                // stdout
                echo $data;
            } else {
                // stderr
                echo $data;
            }
        }

        return $process->getExitCode();
    }
}