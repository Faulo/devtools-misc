<?php
namespace Slothsoft\Unity;

use Slothsoft\Devtools\CLI;
use Slothsoft\Core\DOMHelper;

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
            
            $directory = new \RecursiveDirectoryIterator($path);
            $directoryIterator = new \RecursiveIteratorIterator($directory);
            foreach ($directoryIterator as $directory) {
                if ($directory->isDir()) {
                    $unity = $directory->getRealPath();
                    if (basename($unity) === 'Assets') {
                        $node->setAttribute('unity', dirname($unity));
                        break;
                    }
                }
            }
        }
    }
    public function cloneRepositories() {
        foreach ($this->courseDoc->getElementsByTagName('repository') as $node) {
            $path = $node->getAttribute('path');
            $href = $node->getAttribute('href');
            if (!is_dir($path)) {
                $command = sprintf('git clone %s %s', escapeshellarg($href), escapeshellarg($path));
                CLI::execute($command);
            }
        }
    }
    public function pullRepositories() {
        foreach ($this->courseDoc->getElementsByTagName('repository') as $node) {
            $path = $node->getAttribute('path');
            $git = new GitProject($path);
            $git->pull();
        }
    }
    public function runTests() {
        foreach ($this->courseDoc->getElementsByTagName('repository') as $node) {
            $unity = $node->getAttribute('unity');
            $results = $node->getAttribute('results');
            
            $project = new UnityProject($this->settings['hub'], $unity);
            $project->runTests($results, 'PlayMode');
        }
    }
}