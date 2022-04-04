<?php
namespace Slothsoft\Devtools\Update\Fix;

use Slothsoft\Devtools\Update\UpdateInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;

class FixTestsCreate implements UpdateInterface {

    const TEST_STUB_CLASS = <<<'EOF'
<?php
declare(strict_types = 1);
namespace %1$s;

use PHPUnit\Framework\TestCase;

/**
 * %2$s
 *
 * @see %3$s
 *
 * @todo auto-generated
 */
class %2$s extends TestCase {

    public function testClassExists(): void {
        $this->assertTrue(class_exists(%3$s::class), "Failed to load class '%1$s\%3$s'!");
    }
}
EOF;

    const TEST_STUB_INTERFACE = <<<'EOF'
<?php
declare(strict_types = 1);
namespace %1$s;

use PHPUnit\Framework\TestCase;

/**
 * %2$s
 *
 * @see %3$s
 *
 * @todo auto-generated
 */
class %2$s extends TestCase {

    public function testInterfaceExists(): void {
        $this->assertTrue(interface_exists(%3$s::class), "Failed to load interface '%1$s\%3$s'!");
    }
}
EOF;

    const TEST_STUB_TRAIT = <<<'EOF'
<?php
declare(strict_types = 1);
namespace %1$s;

use PHPUnit\Framework\TestCase;

/**
 * %2$s
 *
 * @see %3$s
 *
 * @todo auto-generated
 */
class %2$s extends TestCase {

    public function testTraitExists(): void {
        $this->assertTrue(trait_exists(%3$s::class), "Failed to load trait '%1$s\%3$s'!");
    }
}
EOF;

    public function runOn(array $project) {
        if (! is_dir($project['sourceDir']) or ! is_dir($project['sourceDir']) or ! $project['namespace']) {
            return;
        }
        $directory = new RecursiveDirectoryIterator($project['sourceDir']);
        $directoryIterator = new RecursiveIteratorIterator($directory);
        $fileIterator = new RegexIterator($directoryIterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);
        foreach ($fileIterator as $file) {
            $sourceFile = realpath($file[0]);
            $info = pathinfo(str_replace($project['sourceDir'], $project['testsDir'], $sourceFile));
            $info['testname'] = $info['filename'] . 'Test';
            $info['namespace'] = $project['namespace'] . dirname(str_replace($project['sourceDir'], '', $sourceFile));
            $info['namespace'] = preg_replace('~\\\.$~', '', $info['namespace']);
            $testFile = $info['dirname'] . DIRECTORY_SEPARATOR . $info['testname'] . '.' . $info['extension'];

            $generate = true;
            if (is_file($testFile)) {
                $code = file_get_contents($testFile);
                if (strpos($code, '@todo auto-generated') === false) {
                    $generate = false;
                }
            }

            if ($generate) {
                echo $testFile . PHP_EOL;
                $template = $this->getTemplate($info['filename']);
                $code = sprintf($template, $info['namespace'], $info['testname'], $info['filename']);
                if (! is_dir(dirname($testFile))) {
                    mkdir(dirname($testFile), 0777, true);
                }
                file_put_contents($testFile, $code);
            }
        }
    }

    private function getTemplate(string $name): string {
        switch (true) {
            case preg_match('~Interface$~', $name) === 1:
                return self::TEST_STUB_INTERFACE;
            case preg_match('~Trait$~', $name) === 1:
                return self::TEST_STUB_TRAIT;
            default:
                return self::TEST_STUB_CLASS;
        }
    }
}

