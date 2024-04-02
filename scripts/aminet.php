<?php
declare(strict_types = 1);

use Slothsoft\Core\DOMHelper;
use Slothsoft\Core\Storage;
use Slothsoft\Core\Calendar\Seconds;
foreach ([
    __DIR__ . '/../../../autoload.php',
    __DIR__ . '/../vendor/autoload.php'
] as $file) {
    if (file_exists($file)) {
        require_once $file;
        break;
    }
}

$paths = [];
$paths['game/2play'] = 'Aminet/Multiplayer';
$paths['game/actio'] = 'Aminet/Action';
$paths['game/jump'] = 'Aminet/Jump\'n\'Run';
$paths['game/misc'] = 'Aminet/Miscellaneous';
$paths['game/race'] = 'Aminet/Racing';
$paths['game/role'] = 'Aminet/Adventure';
$paths['game/shoot'] = 'Aminet/Shoot\'em\'up';
$paths['game/strat'] = 'Aminet/Strategy';
$paths['game/text'] = 'Aminet/Text';

$blacklist = [];
$blacklist[] = 'TwSrc';

const DIR_DOWNLOADS = 'Aminet/Downloads/';

const SEVENZIP = 'C:\\Program Files\\7-Zip\\7z.exe';

const AMINET_DOMAIN = 'http://aminet.net';

const AMINET_SEARCH = '/search?path[]=%s&arch[]=m68k-amigaos&start=%d';

if (! is_dir(DIR_DOWNLOADS)) {
    mkdir(DIR_DOWNLOADS, 0777, true);
    copy(__DIR__ . DIRECTORY_SEPARATOR . 'folder.info', DIR_DOWNLOADS . '.info');
}

foreach ($paths as $path => $folder) {
    $packages = [];
    for ($i = 0;; $i += 50) {
        $uri = sprintf(AMINET_DOMAIN . AMINET_SEARCH, $path, $i);
        $document = Storage::loadExternalDocument($uri, Seconds::MONTH);
        $xpath = DOMHelper::loadXPath($document);

        $nodes = $xpath->evaluate('//*[@id="start_listing"]/*[@class]');
        if (! $nodes->length) {
            break;
        }
        foreach ($nodes as $node) {
            $package = [];
            $package['file'] = DIR_DOWNLOADS . $xpath->evaluate('normalize-space(*[1])', $node);
            $package['description'] = $xpath->evaluate('normalize-space(*[last()])', $node);
            $package['href'] = AMINET_DOMAIN . $xpath->evaluate('normalize-space(.//@href)', $node);
            $package['name'] = pathinfo($package['file'], PATHINFO_FILENAME);
            $package['extension'] = pathinfo($package['file'], PATHINFO_EXTENSION);
            $package['folder'] = $folder . DIRECTORY_SEPARATOR . $package['name'];

            $isDemo = stripos($package['description'], 'demo') != false;
            $isWin = ($package['extension'] === 'zip' or $package['extension'] === 'exe');
            $isBlack = in_array($package['name'], $blacklist);

            $package['skip'] = ($isDemo or $isWin or $isBlack);
            $packages[] = $package;
        }
    }
    if ($packages) {
        echo $folder . PHP_EOL;
        if (! is_dir($folder)) {
            mkdir($folder, 0777, true);
            copy(__DIR__ . DIRECTORY_SEPARATOR . 'folder.info', $folder . '.info');
        }
        foreach ($packages as $package) {
            echo $package['name'];
            if ($package['skip']) {
                echo '...SKIP';
                if (file_exists($package['folder'])) {
                    passthru("recycle.exe $package[folder]");
                }
                if (file_exists($package['folder'] . '.info')) {
                    passthru("recycle.exe $package[folder].info");
                }
            } else {
                if (file_exists($package['folder'] . '.info')) {
                    echo '...OK';
                    if (! file_exists($package['folder'])) {
                        unlink($package['folder'] . '.info');
                    }
                } else {
                    echo '...DOWNLOAD';
                    if (! file_exists($package['file'])) {
                        file_put_contents($package['file'], file_get_contents($package['href']));
                    }
                    $command = sprintf('%s x %s -o%s', escapeshellarg(SEVENZIP), escapeshellarg($package['file']), escapeshellarg($package['folder']));
                    echo '  ' . $command . PHP_EOL;
                    echo `$command`;
                    if (file_exists($package['folder'])) {
                        copy('folder.info', $package['folder'] . '.info');
                    }
                }
            }
            echo PHP_EOL;
        }
        echo PHP_EOL;
    }
}

echo '...done!';