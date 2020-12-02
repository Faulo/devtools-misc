<?php
namespace Slothsoft\Devtools\Update\Fix;

use Slothsoft\Devtools\Update\UpdateInterface;

class FixDocsDelete implements UpdateInterface {

    private function getDeprecatedFiles(array $project): array {
        return [
            $project['workspaceDir'] . 'docs'
        ];
    }

    public function runOn(array $project) {
        foreach ($this->getDeprecatedFiles($project) as $file) {
            if (file_exists($file)) {
                exec('Recycle.exe -f ' . escapeshellarg($file));
            }
        }
    }
}

