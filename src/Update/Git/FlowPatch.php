<?php
namespace Slothsoft\Devtools\Misc\Update\Git;

use Slothsoft\Devtools\Misc\Utils;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;
use Slothsoft\Devtools\Misc\Update\Project;

class FlowPatch implements UpdateInterface {

    public const MAJOR_RELEASE = 0;

    public const MINOR_RELEASE = 1;

    public const PATCH_RELEASE = 2;

    private int $release;

    private string $message;

    public function __construct(int $release = self::PATCH_RELEASE, $message = 'minor fixes') {
        $this->release = $release;
        $this->message = $message;
    }

    public function runOn(Project $project) {
        if ($project->chdir() and is_dir('.git')) {
            $version = exec('git describe --tags --abbrev=0');
            $version = explode('.', $version);
            $version[$this->release] ++;
            for ($i = $this->release + 1; $i <= 2; $i ++) {
                $version[$i] = 0;
            }
            $version = implode('.', $version);

            Utils::execute("git flow release start $version");
            Utils::execute("git flow release finish $version -p -m \"$this->message\"");
        }
    }
}

