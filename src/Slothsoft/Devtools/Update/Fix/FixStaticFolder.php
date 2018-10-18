<?php
namespace Slothsoft\Devtools\Update\Fix;

use Slothsoft\Devtools\Update\UpdateInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Slothsoft\Devtools\Composer\ComposerManifest;


class FixStaticFolder implements UpdateInterface
{
    private function getDeprecatedFiles(array $project) : array {
        return [
            //$project['workspaceDir'] . 'composer.phar',
            //$project['workspaceDir'] . 'run-tests.launch',
            //$project['slothsoftDir']
        ];
    }
    private function getDirectCopyFiles() : array {
        return [
            'composer.phar',
        ];
    }
    
    public function runOn(array $project)
    {
        if (isset($project['standalone'])) {
            return;
        }
        $args = [];
        $args[] = $project['vendor'];
        $args[] = $project['name'];
        $args[] = $project['homeUrl'];
        if (preg_match('~[a-z0-9]+~', $project['name'], $match)) {
            $args[] = $match[0];
        } else {
            $args[] = $project['name'];
        }
        
        $directCopyFiles = $this->getDirectCopyFiles();
        
        $files = $this->getStaticFiles($project);
        foreach ($files as $source => $target) {
            if (is_dir($source))  {
                if (!is_dir($target)) {
                    echo $target . PHP_EOL;
                    mkdir($target, 0777, true);
                }
            } else {
                if (in_array(basename($source), $directCopyFiles)) {
                    if (!file_exists($target) or md5_file($source) !== md5_file($target)) {
                        echo $target . PHP_EOL;
                        copy($source, $target);
                    }
                } else {
                    $contents = file_get_contents($source);
                    $contents = vsprintf($contents, $args);
                    if (!file_exists($target) or $contents !== file_get_contents($target)) {
                        echo $target . PHP_EOL;
                        file_put_contents($target, $contents);
                    }
                }
            }
        }
        foreach ($this->getDeprecatedFiles($project) as $file) {
            if (file_exists($file)) {
                echo "RECYCLING $file" . PHP_EOL;
                exec('Recycle.exe -f ' . escapeshellarg($file));
            }
        }
    }
    
    private function getStaticFiles(array $project) : array {
        $composer = new ComposerManifest($project['composerFile']);
        $composer->load();
        $typeList = $composer->getKeywords();
        
        $ret = [];
        
        foreach ($typeList as $type) {
            $baseDirectory = $project['staticDir'] . $type;
            $baseDirectory = realpath($baseDirectory);
            
            if (!$baseDirectory) {
                continue;
            }
            
            $projectDirectory = realpath($project['workspaceDir']);
            assert((bool) $projectDirectory);
            
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($baseDirectory));
            foreach ($iterator as $file) {
                $path = $file->getPathname();
                $ret[$path] = str_replace($baseDirectory, $projectDirectory, $path);
            }
        }
        return $ret;
    }
}

