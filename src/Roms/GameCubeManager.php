<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Roms;

use Slothsoft\Core\FileSystem;
use Slothsoft\Devtools\Misc\Utils;
use Symfony\Component\Process\Process;

class GameCubeManager extends RomManagerBase {

    public const DOLPHIN_EXE = 'DolphinTool.exe';

    public const DOLPHIN_FORMAT = 'iso';

    public const CONVERT_RUN = true;

    public const WIT_EXE = 'C:\\Program Files\\Wiimm\\WIT\\wit.exe';

    public const PACK_RUN = true;

    public const NKIT_EXE = 'C:\\Users\\Daniel\\GoogleDrive\\Symlinks\\NKit\\ConvertToNKit.exe';

    public const TRANSLATIONS = [
        'MarioParty4' => 'Mario Party 4',
        'Resident Evil' => 'Resident Evil 1',
        'resident evil 4 game disc 1' => 'Resident Evil 4',
        'resident evil 4 game disc 2' => 'Resident Evil 4',
        'TALES OF SYMPHONIA 1' => 'Tales of Symphonia',
        'TALES OF SYMPHONIA 2' => 'Tales of Symphonia',
        'TALES OF SYMPHONIA DISC 1' => 'Tales of Symphonia',
        'THE LEGEND OF ZELDA The Wind Waker for USA' => 'The Legend of Zelda: The Wind Waker',
        'The Legend of Zelda Twilight Princess' => 'The Legend of Zelda: Twilight Princess',
        'ZELDA OCARINA MULTI PACK' => 'The Legend of Zelda: Ocarina of Time Multipack',
        'Timesplitters Future Perfect' => 'TimeSplitters: Future Perfect',
        'FIRE EMBLEM GC EU' => 'Fire Emblem: Path of Radiance',
        'SonicAdventureDX' => 'Sonic Adventure 1 DX',
        'SonicRiders' => 'Sonic Riders',
        'SONIC HEROES' => 'Sonic Heroes',
        'SONIC GEMS COLLECTION' => 'Sonic Gems Collection',
        'SOULCALIBUR2' => 'Soulcalibur II',
        'SSX3' => 'SSX 3'
    ];

    private const SUPPORTED_EXTENSIONS = [
        'iso',
        'rvz'
    ];

    public function doesSupport(string $extension): bool {
        return in_array($extension, self::SUPPORTED_EXTENSIONS);
    }

    public function romInfo(string $inputFile): RomInfo {
        echo $inputFile . PHP_EOL;

        $disc = '';
        $match = [];
        if (preg_match('~Disc (\d)~', $inputFile, $match)) {
            $disc = " Disc $match[1]";
        }

        $process = new Process([
            self::DOLPHIN_EXE,
            'header',
            '--input',
            $inputFile
        ]);
        $process->run();
        $result = $process->getOutput();

        $data = [];
        foreach (explode(PHP_EOL, $result) as $row) {
            $row = explode(':', $row, 2);
            if (count($row) == 2) {
                $data[trim($row[0])] = trim($row[1]);
            }
        }

        $name = $data['Internal Name'];
        if (isset(self::TRANSLATIONS[$name])) {
            $name = self::TRANSLATIONS[$name];
        }
        $id = $data['Game ID'];
        $region = $data['Country'];

        $name = str_replace(': ', ' - ', $name);
        $name = FileSystem::filenameSanitize($name);

        $data['Path'] = $inputFile;
        $data['Output Name'] = "$name ($region) [$id]$disc";

        return new RomInfo($data['Path'], $data['Output Name']);
    }

    public function getCommand(string $command): CommandBase {
        switch ($command) {
            case 'convert':
                return new ConvertGC();
            case 'pack':
                return new PackGC();
            case 'store':
                return new StoreGC();
        }

        throw new \BadMethodCallException($command);
    }
}

class ConvertGC extends CommandBase {

    public function do(RomInfo $input, string $outputDirectory): void {
        $inputFile = $input->realpath;
        $outputName = $input->name;

        $outputFile = $outputDirectory . DIRECTORY_SEPARATOR . $outputName . '.' . GameCubeManager::DOLPHIN_FORMAT;

        if (! file_exists($outputFile)) {
            echo $outputName . PHP_EOL;

            $process = new Process([
                GameCubeManager::DOLPHIN_EXE,
                'convert',
                '--input',
                $inputFile,
                '--output',
                $outputFile,
                '--format',
                GameCubeManager::DOLPHIN_FORMAT
            ]);
            $process->setTimeout(300);

            if (GameCubeManager::CONVERT_RUN) {
                $process->run();
            } else {
                file_put_contents($outputFile . '.txt', $process->getCommandLine());
            }

            $result = $process->getExitCode();
            if ($result) {
                throw new \Exception("ERROR: $result" . PHP_EOL . $process->getCommandLine());
            }

            echo PHP_EOL;
        }
    }
}

class PackGC extends CommandBase {

    public function do(RomInfo $input, string $outputDirectory): void {
        $inputFile = $input->realpath;
        $outputName = $input->name;

        $outputFile = $outputDirectory . DIRECTORY_SEPARATOR . $outputName . '.nkit.iso';

        if (file_exists($outputFile)) {
            return;
        }

        $tempFile = $outputDirectory . DIRECTORY_SEPARATOR . $outputName . '.iso';
        Utils::execute(sprintf('xcopy /Y %s %s', escapeshellarg($inputFile), escapeshellarg($outputDirectory)));

        echo $outputName . PHP_EOL;

        $process = new Process([
            GameCubeManager::NKIT_EXE,
            $tempFile
        ]);
        $process->setTimeout(300);

        if (GameCubeManager::PACK_RUN) {
            $process->run();
        } else {
            file_put_contents($outputFile . '.txt', $process->getCommandLine());
        }

        $result = $process->getExitCode();
        if ($result and ! file_exists($outputFile)) {
            throw new \Exception("ERROR: $result" . PHP_EOL . $process->getCommandLine());
        }

        echo PHP_EOL;
    }
}

class StoreGC extends CommandBase {

    public function do(RomInfo $input, string $outputDirectory): void {
        $inputFile = $input->realpath;
        $outputName = $input->name;

        $outputFile = $outputDirectory . DIRECTORY_SEPARATOR . $outputName . DIRECTORY_SEPARATOR . 'game.iso';

        if (! file_exists($outputFile)) {
            if (! file_exists($outputDirectory . DIRECTORY_SEPARATOR . $outputName)) {
                mkdir($outputDirectory . DIRECTORY_SEPARATOR . $outputName);
            }

            copy($inputFile, $outputFile);
        }
    }
}