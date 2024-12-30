<?php
declare(strict_types = 1);

use Slothsoft\Core\FileSystem;
use Slothsoft\Devtools\Misc\Utils;
use Symfony\Component\Process\Process;

foreach ([
    __DIR__ . '/../../../autoload.php',
    __DIR__ . '/../vendor/autoload.php'
] as $file) {
    if (file_exists($file)) {
        require_once $file;
        break;
    }
}

const DOLPHIN_EXE = 'DolphinTool.exe';

const DOLPHIN_FORMAT = 'iso';

const CONVERT_RUN = true;

const WIT_EXE = 'C:\\Program Files\\Wiimm\\WIT\\wit.exe';

const PACK_RUN = true;

const NKIT_EXE = 'C:\\Users\\Daniel\\GoogleDrive\\Symlinks\\NKit\\ConvertToNKit.exe';

const TRANSLATIONS = [
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

$supportedImages = [];
$supportedImages[] = 'iso';
$supportedImages[] = 'rvz';
$supportedImages[] = DOLPHIN_FORMAT;

array_shift($_SERVER['argv']);
$_SERVER['argc'] --;

if (count($_SERVER['argv']) < 3) {
    echo <<<'EOT'
    GameCube image converter.
    
    Usage:
        composer run gamecube convert|pack|store "path/to/input" "path/to/gc" "path/to/output"
    
    
    EOT;

    return;
}

$mode = array_shift($_SERVER['argv']);

$input = array_shift($_SERVER['argv']);

if (! realpath($input)) {
    die("Invalid input path: '$input'");
}

$input = realpath($input);

$output = array_shift($_SERVER['argv']);

if (! realpath($output)) {
    die("Invalid output path: '$output'");
}

$output = realpath($output);

$node = FileSystem::asNode($input);

foreach ($node->getElementsByTagName('file') as $file) {
    if (in_array($file->getAttribute('ext'), $supportedImages)) {
        $info = imageInfo($file->getAttribute('path'));

        switch ($mode) {
            case 'convert':
                convertImage($info, $output);
                break;
            case 'pack':
                packImage($info, $output);
                break;
            case 'store':
                storeImage($info, $output);
                break;
        }
    }
}

function imageInfo(string $inputFile): array {
    echo $inputFile . PHP_EOL;

    $disc = '';
    $match = [];
    if (preg_match('~Disc (\d)~', $inputFile, $match)) {
        $disc = " Disc $match[1]";
    }

    $process = new Process([
        DOLPHIN_EXE,
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
    if (isset(TRANSLATIONS[$name])) {
        $name = TRANSLATIONS[$name];
    }
    $id = $data['Game ID'];
    $region = $data['Country'];

    $name = str_replace(': ', ' - ', $name);
    $name = FileSystem::filenameSanitize($name);

    $data['Path'] = $inputFile;
    $data['Output Name'] = "$name ($region) [$id]$disc";

    return $data;
}

function convertImage(array $data, string $outputDirectory): void {
    $inputFile = $data['Path'];
    $outputName = $data['Output Name'];

    $outputFile = $outputDirectory . DIRECTORY_SEPARATOR . $outputName . '.' . DOLPHIN_FORMAT;

    if (! file_exists($outputFile)) {
        echo $outputName . PHP_EOL;

        $process = new Process([
            DOLPHIN_EXE,
            'convert',
            '--input',
            $inputFile,
            '--output',
            $outputFile,
            '--format',
            DOLPHIN_FORMAT
        ]);
        $process->setTimeout(300);

        if (CONVERT_RUN) {
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

function packImage(array $data, string $outputDirectory): void {
    $inputFile = $data['Path'];
    $outputName = $data['Output Name'];

    $outputFile = $outputDirectory . DIRECTORY_SEPARATOR . $outputName . '.nkit.iso';

    if (file_exists($outputFile)) {
        return;
    }

    $tempFile = $outputDirectory . DIRECTORY_SEPARATOR . $outputName . '.iso';
    Utils::execute(sprintf('xcopy /Y %s %s', escapeshellarg($inputFile), escapeshellarg($outputDirectory)));

    echo $outputName . PHP_EOL;

    $process = new Process([
        NKIT_EXE,
        $tempFile
    ]);
    $process->setTimeout(300);

    if (PACK_RUN) {
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

function storeImage(array $data, string $outputDirectory): void {
    $inputFile = $data['Path'];
    $outputName = $data['Output Name'];

    $outputFile = $outputDirectory . DIRECTORY_SEPARATOR . $outputName . DIRECTORY_SEPARATOR . 'game.iso';

    if (! file_exists($outputFile)) {
        if (! file_exists($outputDirectory . DIRECTORY_SEPARATOR . $outputName)) {
            mkdir($outputDirectory . DIRECTORY_SEPARATOR . $outputName);
        }

        copy($inputFile, $outputFile);
    }
}