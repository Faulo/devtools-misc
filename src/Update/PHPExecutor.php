<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update;

use Slothsoft\Core\DOMHelper;
use Slothsoft\Devtools\Misc\Utils;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class PHPExecutor {

    private const CONFIG_FILE = '.settings/org.eclipse.wst.common.project.facet.core.xml';

    private const CONFIG_QUERY = 'string(//*[@facet="php.component"]/@version)';

    public string $version = '7.4';

    public string $executable = '';

    private string $executableTemplate = '/Webserver/php-%s/php.exe';

    public function __construct(?string $workspace = null) {
        if (! $workspace) {
            $workspace = getcwd();
        }

        if (isset($_ENV['PHP_VERSION'])) {
            $this->setVersion($_ENV['PHP_VERSION']);
            return;
        }

        if ($workspace = realpath($workspace)) {
            $file = $workspace . DIRECTORY_SEPARATOR . self::CONFIG_FILE;
            if ($file = realpath($file)) {
                $document = DOMHelper::loadDocument($file);
                $xpath = DOMHelper::loadXPath($document);
                $this->setVersion($xpath->evaluate(self::CONFIG_QUERY));
                return;
            }
        }

        $this->setVersion($this->version);
    }

    public function setVersion(string $version): void {
        if ($version !== '') {
            $this->executable = sprintf($this->executableTemplate, $version);
            $this->version = $version;
            if (! is_file($this->executable)) {
                throw new FileNotFoundException($this->executable);
            }
            $this->executable = realpath($this->executable);
        }
    }

    public function execute(string $command): void {
        Utils::execute("$this->executable $command");
    }

    public function vexecute(string ...$args): void {
        foreach ($args as &$arg) {
            $arg = escapeshellarg($arg);
        }

        $this->execute(implode(' ', $args));
    }

    public function composer(string ...$args): void {
        $phar = is_file('composer.phar') ? 'composer.phar' : __DIR__ . DIRECTORY_SEPARATOR . 'composer.phar';
        $this->vexecute($phar, ...$args);
    }
}

