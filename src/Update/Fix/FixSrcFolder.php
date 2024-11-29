<?php
namespace Slothsoft\Devtools\Misc\Update\Fix;

use Slothsoft\Devtools\Misc\Update\UpdateInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;

class FixSrcFolder implements UpdateInterface {

    public function runOn(array $project) {
        foreach ([
            $project['sourceDir'],
            $project['testsDir']
        ] as $sourceDir) {
            if (is_dir($sourceDir)) {
                $directory = new RecursiveDirectoryIterator($sourceDir);
                $directoryIterator = new RecursiveIteratorIterator($directory);
                $fileIterator = new RegexIterator($directoryIterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);
                foreach ($fileIterator as $file) {
                    $sourceFile = $file[0];
                    $source = file_get_contents($sourceFile);
                    $backup = $source;
                    if (strpos($source, 'declare(strict_types = 1);') === false) {
                        $count = 0;
                        $source = str_replace('<?php', '<?php' . PHP_EOL . 'declare(strict_types = 1);', $source, $count);
                        if ($count !== 1) {
                            throw new \RuntimeException("what's up with $sourceFile?!");
                        }
                    }
                    $source = preg_replace('/' . preg_quote('declare(ticks') . '\s*=\s*\d+' . preg_quote(');') . '/', '', $source);

                    if (strpos($source, 'declare(ticks') !== false) {
                        throw new \RuntimeException("declare(ticks in $sourceFile?!");
                    }

                    if ($source !== $backup) {
                        echo $sourceFile . PHP_EOL;
                        file_put_contents($sourceFile, $source);
                    }
                }
            }
        }
    }
}

