<?php
declare(strict_types = 1);

use Slothsoft\Unity\JsonUtils;
use Slothsoft\Core\DOMHelper;
use Slothsoft\Core\Calendar\DateTimeFormatter;
use Michelf\Markdown;

foreach ([
    __DIR__ . '/../../../autoload.php',
    __DIR__ . '/../vendor/autoload.php'
] as $file) {
    if (file_exists($file)) {
        require_once $file;
        break;
    }
}

$transformers = [];
$transformers['xhtml'] = 'to-html.xsl';
$transformers['md'] = 'to-md.xsl';

if (! is_dir('input')) {
    mkdir('input', 0777);
}

if (! is_dir('output')) {
    mkdir('output', 0777);
}

chdir(__DIR__ . '/output');
exec('del * /q');

chdir(__DIR__);

$dom = new DOMHelper();

$speakers = [];
$speakers['assistant'] = 'ChatGPT';
$speakers['user'] = 'Daniel';
$speakers['unknown'] = '???';

foreach (glob('input/*.json') as $file) {
    $name = pathinfo($file, PATHINFO_FILENAME);
    $data = JsonUtils::load($file);
    foreach ($data as $i => $conv) {
        $time = (int) $conv['create_time'];
        $source = new DOMDocument();
        $root = $source->createElement('conversation');
        $root->setAttribute('title', $conv['title']);
        $root->setAttribute('datetime', date(DateTimeFormatter::FORMAT_DATETIME, $time));
        foreach ($conv['mapping'] as $message) {
            $parts = $message['message']['content']['parts'] ?? [];
            $text = implode(PHP_EOL, $parts);
            if ($text) {
                $time = (int) $message['message']['create_time'];
                $node = $source->createElement('message');
                $node->setAttribute('text', $text);
                $html = Markdown::defaultTransform(htmlentities($text, ENT_XML1));
                $html = "<div xmlns=\"http://www.w3.org/1999/xhtml\">$html</div>";
                $node->appendChild($dom->parse($html, $source, false));
                $role = $message['message']['author']['role'] ?? 'unknown';
                $node->setAttribute('speaker-role', $role);
                $node->setAttribute('speaker-name', $speakers[$role]);
                $node->setAttribute('datetime', date(DateTimeFormatter::FORMAT_DATETIME, $time));
                $root->appendChild($node);
            }
        }
        $source->appendChild($root);

        $file = "output/$name.$i.json";
        file_put_contents($file, json_encode($conv, JSON_PRETTY_PRINT));
        touch($file, $time);

        foreach ($transformers as $ext => $template) {
            $file = new SplFileInfo("output/$name.$i.$ext");
            echo $file . PHP_EOL;
            $dom->transformToFile($source, $template, [], $file);
            touch((string) $file, $time);
        }
    }
}

