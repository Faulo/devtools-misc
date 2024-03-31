<?php
namespace Slothsoft\Devtools\Misc\Update;

use Slothsoft\Core\DOMHelper;

class PHPExecutor {

    private const CONFIG_FILE = '.settings/org.eclipse.wst.common.project.facet.core.xml';

    private const CONFIG_QUERY = 'string(//*[@facet="php.component"]/@version)';

    private string $version = '7.4';

    private string $executable = '/Webserver/php-7.4/php.exe';

    public function __construct(?string $workspace = null) {
        if (! $workspace) {
            $workspace = getcwd();
        }

        if ($workspace = realpath($workspace)) {
            $file = $workspace . DIRECTORY_SEPARATOR . self::CONFIG_FILE;
            if ($file = realpath($file)) {
                $document = DOMHelper::loadDocument($file);
                $xpath = DOMHelper::loadXPath($document);
                $version = $xpath->evaluate(self::CONFIG_QUERY);
                if ($version !== '') {
                    $this->executable = str_replace($this->version, $version, $this->executable);
                    $this->version = $version;
                }
            }
        }

        $this->executable = realpath($this->executable);
    }

    public function execute(string $command): void {
        passthru("$this->executable $command");
    }
}

