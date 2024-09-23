<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Git;

use Slothsoft\Devtools\Misc\Utils;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;
use Slothsoft\Devtools\Misc\Update\Project;

class TagPatch implements UpdateInterface {

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
            $description = exec('git describe --tags --long');
            $description = explode('-', $description);
            $oldVersion = $description[0];
            $count = (int) $description[1];

            $version = explode('.', $oldVersion);
            if ($count > 0) {
                $version[$this->release] ++;
                for ($i = $this->release + 1; $i <= 2; $i ++) {
                    $version[$i] = 0;
                }
            }
            $newVersion = implode('.', $version);

            if ($oldVersion !== $newVersion) {
                Utils::execute("git tag $newVersion");
                Utils::execute("git push --progress origin tag $newVersion");
            }
        }
    }
}

