<?php
namespace Slothsoft\Devtools;

use Symfony\Component\Process\Process;

class CLI {

    public static function execute(string $command): int {
        echo PHP_EOL . PHP_EOL . '> ' . $command . PHP_EOL;
        $process = Process::fromShellCommandline($command);
		$process->setTimeout(24*3600);
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