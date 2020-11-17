<?php
namespace Slothsoft\Unity;

use Slothsoft\Devtools\CLI;
use Slothsoft\Core\DOMHelper;
use SplFileInfo;

class UnityCourse {

    private $resultsFolder;

    private $courseDoc;

    private $settings = [];

    public function __construct(string $xmlFile, string $resultsFolder) {
        assert(is_file($xmlFile));
        assert(is_dir($resultsFolder));

        $this->resultsFolder = realpath($resultsFolder);
        $this->loadSettings($xmlFile);
    }

    private function loadSettings(string $xmlFile) {
        $this->courseDoc = DOMHelper::loadDocument($xmlFile);
        $xpath = DOMHelper::loadXPath($this->courseDoc);
        $this->settings['hub'] = $xpath->evaluate('string(//unity/@hub)');
        $this->settings['workspace'] = $xpath->evaluate('string(//unity/@workspace)');
        $this->settings['project'] = $xpath->evaluate('string(//unity/@project)');

        assert(is_dir($this->settings['hub']));
        assert(is_dir($this->settings['workspace']));

        $this->settings['hub'] = realpath($this->settings['hub']);
        $this->settings['workspace'] = realpath($this->settings['workspace']);

        foreach ($this->courseDoc->getElementsByTagName('repository') as $node) {
            $name = $node->getAttribute('name');
            $path = $this->settings['workspace'] . DIRECTORY_SEPARATOR . $this->settings['project'] . '.' . $name;
            $results = $this->resultsFolder . DIRECTORY_SEPARATOR . $name . '.xml';
            $node->setAttribute('path', $path);
            $node->setAttribute('results', $results);

            if ($unity = $this->findUnityPath($path)) {
                $node->setAttribute('unity', $unity);
            }
        }
    }

    private function findUnityPath(string $path) {
        if (is_dir($path)) {
            $directory = new \RecursiveDirectoryIterator($path);
            $directoryIterator = new \RecursiveIteratorIterator($directory);
            foreach ($directoryIterator as $directory) {
                if ($directory->isDir()) {
                    $unity = $directory->getRealPath();
                    if (basename($unity) === 'Assets') {
                        return dirname($unity);
                    }
                }
            }
        }
        return null;
    }

    public function cloneRepositories() {
        foreach ($this->courseDoc->getElementsByTagName('repository') as $node) {
            $path = $node->getAttribute('path');
            $href = $node->getAttribute('href');
            if (! is_dir($path)) {
                $command = sprintf('git clone %s %s', escapeshellarg($href), escapeshellarg($path));
                CLI::execute($command);
                if ($unity = $this->findUnityPath($path)) {
                    $node->setAttribute('unity', $unity);
                }
                sleep(1);
            }
        }
    }

    public function pullRepositories() {
        foreach ($this->courseDoc->getElementsByTagName('repository') as $node) {
            $path = $node->getAttribute('path');
            $git = new GitProject($path);
            $git->pull();
            $git->reset();
            $branch = $git->branches()[0];
            // git checkout -B master --track origin/master
            $git->execute("checkout -B $branch --track origin/$branch");
        }
    }

    public function runTests() {
        foreach ($this->courseDoc->getElementsByTagName('repository') as $node) {
            if (! $node->hasAttribute('unity')) {
                continue;
            }
            $unity = $node->getAttribute('unity');
            $results = $node->getAttribute('results');

            $project = new UnityProject($this->settings['hub'], $unity);
            $project->runTests($results, 'PlayMode');
        }
    }

    public function writeReport(string $dataFile, string $templateFile, string $outputFile) {
        $reportDoc = new \DOMDocument();
        $rootNode = $reportDoc->createElement('report');
        foreach (range(1, 13) as $i) {
            $node = $reportDoc->createElement('test-id');
            $node->textContent = sprintf('Testat%02d', $i);
            $rootNode->appendChild($node);
        }
        foreach ($this->courseDoc->getElementsByTagName('repository') as $node) {
            if (! $node->hasAttribute('unity')) {
                continue;
            }
            $results = $node->getAttribute('results');

            if (is_file($results)) {
                if ($resultsDoc = DOMHelper::loadDocument($results)) {
                    $resultsNode = $reportDoc->importNode($node, true);
                    $resultsNode->appendChild($reportDoc->importNode($resultsDoc->documentElement, true));
                    $rootNode->appendChild($resultsNode);
                }
            }
        }
        $reportDoc->appendChild($rootNode);
        $reportDoc->save($dataFile);

        $dom = new DOMHelper();
        $dom->transformToFile($reportDoc, $templateFile, [], new SplFileInfo($outputFile));
    }

    public function requestTest(string $testsFolder, int $testNumber) {
        $testName = sprintf('Testat%02d', $testNumber);
        $branchName = "exam/$testName";
        $testFolder = $testsFolder . DIRECTORY_SEPARATOR . $testName;
        assert(is_dir($testFolder));
        $testFolder = realpath($testFolder);

        foreach ($this->courseDoc->getElementsByTagName('repository') as $node) {
            if (! $node->hasAttribute('unity')) {
                continue;
            }
            $unity = $node->getAttribute('unity');
            $path = $node->getAttribute('path');

            $git = new GitProject($path);
            $git->pull();

            $git->branch($branchName, true);

            $directory = new \RecursiveDirectoryIterator($testFolder);
            $directoryIterator = new \RecursiveIteratorIterator($directory);
            foreach ($directoryIterator as $file) {
                if ($file->isFile()) {
                    $path = $file->getRealPath();
                    assert(strpos($path, $testFolder) === 0);
                    $path = substr($path, strlen($testFolder));
                    echo $path . PHP_EOL;
                    copy($testFolder . $path, $unity . $path);
                }
            }

            $git->add();
            $git->commit("Fix $testName");
        }
    }
}