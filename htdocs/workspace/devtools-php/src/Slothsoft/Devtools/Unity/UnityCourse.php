<?php
namespace Slothsoft\Devtools\Unity;

use Slothsoft\Core\DOMHelper;
use SplFileInfo;

class UnityCourse {

    public $resultsFolder;

    private $courseDoc;

    public $settings;

    private $students;

    public function __construct(string $xmlFile, string $resultsFolder) {
        assert(is_file($xmlFile));
        assert(is_dir($resultsFolder));

        $this->resultsFolder = realpath($resultsFolder);
        $this->loadSettings($xmlFile);
        $this->loadStudents();
    }

    private function loadSettings(string $xmlFile) {
        $this->settings = [];
        $this->courseDoc = DOMHelper::loadDocument($xmlFile);
        $xpath = DOMHelper::loadXPath($this->courseDoc);
        $this->settings['hub'] = $xpath->evaluate('string(//unity/@hub)');
        $this->settings['workspace'] = $xpath->evaluate('string(//unity/@workspace)');
        $this->settings['project'] = $xpath->evaluate('string(//unity/@project)');

        assert(is_dir($this->settings['hub']));
        assert(is_dir($this->settings['workspace']));

        $this->settings['hub'] = realpath($this->settings['hub']);
        $this->settings['workspace'] = realpath($this->settings['workspace']);
    }

    private function loadStudents() {
        $this->students = [];
        foreach ($this->courseDoc->getElementsByTagName('repository') as $node) {
            $this->students[] = new UnityCourseStudent($this, $node);
        }
    }

    // Student stuff
    public function getStudents($cloneIfNeeded = false, $includeInvalid = false): iterable {
        foreach ($this->students as $student) {
            if ($cloneIfNeeded and (! $student->git or ! $student->unity)) {
                $student->download();
            }
            if ($includeInvalid or ($student->git and $student->unity)) {
                yield $student;
            }
        }
    }

    public function getGitProjects($cloneIfNeeded = false): iterable {
        foreach ($this->getStudents($cloneIfNeeded) as $student) {
            yield $student->git;
        }
    }

    public function getUnityProjects($cloneIfNeeded = false): iterable {
        foreach ($this->getStudents($cloneIfNeeded) as $student) {
            yield $student->unity;
        }
    }

    // Git stuff
    public function pullRepositories() {
        foreach ($this->getGitProjects(true) as $git) {
            $git->reset();
            $git->pull();
            $git->checkoutLatest();
        }
    }

    public function pushRepositories() {
        foreach ($this->getGitProjects(false) as $git) {
            $git->push();
            $git->reset();
        }
    }

    // Unity stuff
    public function runTests() {
        foreach ($this->getStudents(false, true) as $student) {
            $student->runTests();
        }
    }

    public function deleteFolder(string $folder) {
        foreach ($this->getUnityProjects(true) as $unity) {
            $unity->deleteFolder($folder);
        }
    }

    public function writeReport(string $dataFile, string $templateFile, string $outputFile, $checkForDuplicates = false) {
        $reportDoc = new \DOMDocument();
        $rootNode = $reportDoc->createElement('report');
        foreach (range(1, 13) as $i) {
            $node = $reportDoc->createElement('test-id');
            $node->textContent = sprintf('Test%02d', $i);
            $rootNode->appendChild($node);
        }
        $storage = [];
        $duplicates = [];
        $classes = [];
        foreach ($this->getStudents(true, true) as $student) {
            $classes[] = $student->node->getAttribute('class');
            $name = $student->node->getAttribute('name');

            if ($checkForDuplicates and $student->unity) {
                $unity = $student->node->getAttribute('unity');
                foreach ($student->unity->getAssetFiles() as $file) {
                    if ($file->getExtension() === 'cs') {
                        $path = $file->getRealPath();
                        $location = substr($path, strlen($unity) + 1);
                        $hash = md5_file($path);
                        if (isset($storage[$hash])) {
                            if (! isset($duplicates[$hash])) {
                                $duplicates[$hash] = file_get_contents($path);
                            }
                        } else {
                            $storage[$hash] = [];
                        }
                        $storage[$hash][$name] = $location;
                    }
                }
            }

            $results = $student->node->getAttribute('results');

            if (is_file($results)) {
                if ($resultsDoc = DOMHelper::loadDocument($results)) {
                    $resultsNode = $reportDoc->importNode($student->node, true);
                    $resultsNode->appendChild($reportDoc->importNode($resultsDoc->documentElement, true));
                    $rootNode->appendChild($resultsNode);
                }
            }
        }
        foreach (array_unique($classes) as $class) {
            $node = $reportDoc->createElement('class');
            $node->textContent = $class;
            $rootNode->appendChild($node);
        }
        foreach ($duplicates as $hash => $content) {
            $fileNode = $reportDoc->createElement('duplicate');
            $fileNode->setAttribute('content', $content);
            foreach ($storage[$hash] as $author => $location) {
                $node = $reportDoc->createElement('file');
                $node->setAttribute('author', $author);
                $node->setAttribute('location', $location);
                $fileNode->appendChild($node);
            }
            $rootNode->appendChild($fileNode);
        }
        $reportDoc->appendChild($rootNode);
        $reportDoc->save($dataFile);

        $dom = new DOMHelper();
        $dom->transformToFile($reportDoc, $templateFile, [], new SplFileInfo($outputFile));
    }

    public function requestTest(string $testsFolder, int $testNumber, string $commitMessage) {
        $testName = sprintf('Testat%02d', $testNumber);
        $branchName = "exam";
        $testFolder = $testsFolder . DIRECTORY_SEPARATOR . $testName;
        assert(is_dir($testFolder));
        $testFolder = realpath($testFolder);

        foreach ($this->getStudents(true) as $student) {
            if (! $student->unity) {
                trigger_error("Student {$student->node->getAttribute('name')} does not a valid Unity project, what's up?", E_USER_WARNING);
                continue;
            }
            if ($student->node->hasAttribute('master')) {
                continue;
            }
            $unity = $student->node->getAttribute('unity');
            $path = $student->node->getAttribute('path');

            $student->git->pull();

            $student->git->branch($branchName, true);
            $student->git->mergeLatest();

            $directory = new \RecursiveDirectoryIterator($testFolder);
            $directoryIterator = new \RecursiveIteratorIterator($directory);
            foreach ($directoryIterator as $file) {
                if ($file->isFile()) {
                    $path = $file->getRealPath();
                    assert(strpos($path, $testFolder) === 0);
                    $path = substr($path, strlen($testFolder));
                    echo $path . PHP_EOL;
                    if (! is_dir(dirname($unity . $path))) {
                        mkdir(dirname($unity . $path), 0777, true);
                    }
                    copy($testFolder . $path, $unity . $path);
                }
            }

            $student->git->add();
            $student->git->commit($commitMessage);
            $student->git->push("--set-upstream origin $branchName");
        }
    }
}