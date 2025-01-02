<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Roms;

use Slothsoft\Core\FileSystem;
use Symfony\Component\Process\Process;

class Nintendo64Manager extends RomManagerBase {

    public const ROM64_EXE = 'rom64.exe';

    public const ROM64_FORMAT = 'z64';

    public const TRANSLATIONS = [
        'THE LEGEND OF ZELDA' => 'Zelda - Ocarina of Time',
        'ZELDA MAJORA\'S MASK' => 'Zelda - Majora\'s Mask',
        'BANJO TOOIE' => 'Banjo-Tooie',
        'CONKER BFD' => 'Conker\'s Bad Fur Day',
        'DONKEY KONG 64' => 'Donkey Kong 64',
        'MarioParty' => 'Mario Party 1',
        'MarioParty2' => 'Mario Party 2',
        'MarioParty3' => 'Mario Party 3',
        'Pilot Wings64' => 'Pilotwings 64',
        'Turok 2: Seeds of Ev' => 'Turok 2 - Seeds of Evil',
        'TUROK_DINOSAUR_HUNTE' => 'Turok - Dinosaur Hunter',
        'BOMBERMAN64U' => 'Bomberman 64',
        'HARVESTMOON64' => 'Harvest Moon 64',
        'TONY HAWK PRO SKATER' => 'Tony Hawk\'s Pro Skater 1',
        'THPS2' => 'Tony Hawk\'s Pro Skater 2',
        'THPS3' => 'Tony Hawk\'s Pro Skater 3'
    ];

    private const SUPPORTED_EXTENSIONS = [
        'z64',
        'n64',
        'v64'
    ];

    public function doesSupport(string $extension): bool {
        return in_array($extension, self::SUPPORTED_EXTENSIONS);
    }

    public function romInfo(string $inputFile): RomInfo {
        echo $inputFile . PHP_EOL;

        $process = new Process([
            self::ROM64_EXE,
            'info',
            $inputFile
        ]);
        $process->run();
        $result = $process->getOutput();

        $data = [];
        foreach (explode("\n", $result) as $row) {
            $row = explode(':', $row, 2);
            if (count($row) == 2) {
                $key = trim($row[0]);
                $value = trim($row[1]);
                if ($key !== '' and $value !== '') {
                    $data[$key] = $value;
                }
            }
        }

        $name = $data['Title'];
        if (isset(self::TRANSLATIONS[$name])) {
            $name = self::TRANSLATIONS[$name];
        }
        $name = str_replace(': ', ' - ', $name);
        $name = FileSystem::filenameSanitize($name);

        $region = $data['Region'];
        $id = $data['ID'];

        $name = "$name ($region) [$id]";

        $info = new RomInfo($inputFile, $name);
        $info->format = $data['Format'];
        return $info;
    }

    public function getCommand(string $command): CommandBase {
        switch ($command) {
            case 'convert':
                return new ConvertN64();
        }

        throw new \BadMethodCallException($command);
    }
}

class ConvertN64 extends CommandBase {

    public function do(RomInfo $input, string $outputDirectory): void {
        $inputFile = $input->realpath;
        $outputName = $input->name;

        $outputFile = $outputDirectory . DIRECTORY_SEPARATOR . $outputName . '.' . Nintendo64Manager::ROM64_FORMAT;
        $tempFile = $outputDirectory . DIRECTORY_SEPARATOR . $outputName . '.tmp';

        if (! file_exists($outputFile)) {
            echo $outputName . PHP_EOL;

            if ($input->format === 'z64 (Big-endian)') {
                copy($inputFile, $outputFile);
                var_dump($outputFile);
            } else {
                copy($inputFile, $tempFile);

                $process = new Process([
                    Nintendo64Manager::ROM64_EXE,
                    'convert',
                    $tempFile
                ]);
                $process->setTimeout(300);

                $process->run();

                unlink($tempFile);

                $result = $process->getExitCode();
                if ($result and ! file_exists($outputFile)) {
                    throw new \Exception("ERROR: $result" . PHP_EOL . $process->getCommandLine() . PHP_EOL . $process->getErrorOutput());
                }
            }

            echo PHP_EOL;
        }
    }
}