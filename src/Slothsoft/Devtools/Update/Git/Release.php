<?php
namespace Slothsoft\Devtools\Update\Git;

use Slothsoft\Devtools\Composer\ComposerManifest;
use Slothsoft\Devtools\Update\UpdateInterface;

class Release implements UpdateInterface {

    private $version;

    public function __construct(?string $version = null) {
        $this->version = $version;
    }

    public function runOn(array $project) {
        $status = `git status`;
        $good = <<<EOT
On branch develop
Your branch is up to date with 'origin/develop'.

nothing to commit, working tree clean
EOT;
        if (preg_replace('~\s+~', '', $status) !== preg_replace('~\s+~', '', $good)) {
            echo 'ERROR: Must be on develop and have committed all files.' . PHP_EOL;
            echo $status;
            die();
        }

        $composer = new ComposerManifest($project['composerFile']);
        $composer->load();
        $version = $composer->getVersion();

        if ($this->version === null) {
            $this->version = $version;
        } else {
            if ($version !== $this->version) {
                echo 'ERROR: Must have set version in composer.json.' . PHP_EOL;
                echo "Version is '$version', should be '$this->version'" . PHP_EOL;
                die();
            }
        }

        $this->exec('git checkout master');

        $this->exec('git merge --no-ff develop');

        $this->exec("git tag -f -a $this->version -m \"Release of $this->version\"");

        $this->exec('git push');

        $this->exec("git push -f origin master:refs/tags/$this->version");

        $this->exec('git checkout develop');
    }

    private function exec(string $command) {
        $status = 0;
        passthru($command, $status);
        if ($status !== 0) {
            echo "ERROR running command '$command'." . PHP_EOL;
            die();
        }
    }
}

