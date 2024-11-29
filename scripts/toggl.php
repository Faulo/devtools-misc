<?php
declare(strict_types = 1);

use Slothsoft\Core\FileSystem;
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

array_shift($_SERVER['argv']);
$_SERVER['argc'] --;

$input = 'toggl-input';
$output = 'toggl-output';

foreach (FileSystem::scanDir($input) as $file) {
    echo $file . PHP_EOL;

    if ($path = realpath($input . DIRECTORY_SEPARATOR . $file)) {
        if (($handle = fopen($path, "r")) !== false) {
            $table = [];
            $header = fgetcsv($handle, 1000, ",");
            $start = null;
            $stop = null;
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $row = [];
                foreach ($header as $i => $key) {
                    $row[$key] = $data[$i];
                }

                $timestamp = strtotime($row['Start date'] . ' ' . $row['Start time']);
                $table[$timestamp] = $row;

                $timestamp = strtotime($row['Start date']);
                if ($start === null or $start > $timestamp) {
                    $start = $timestamp;
                }
                if ($stop === null or $stop < $timestamp) {
                    $stop = $timestamp;
                }
            }
            fclose($handle);

            $times = [];
            for ($t = $start; $t < $stop + Seconds::DAY; $t += Seconds::DAY) {
                $date = date('d.m.Y', $t);
                $row = [];
                $row[] = $date;
                foreach ($table as $timestamp => $entry) {
                    if ($timestamp >= $t and $timestamp < $t + Seconds::DAY) {
                        $row[] = $entry['Start time'];
                        $row[] = $entry['End time'];
                    }
                }
                $times[$date] = $row;
            }

            $path = $output . DIRECTORY_SEPARATOR . $file;
            if (($handle = fopen($path, "w")) !== false) {
                foreach ($times as $row) {
                    fputcsv($handle, $row);
                }
                fclose($handle);
            }
        }
    }
}