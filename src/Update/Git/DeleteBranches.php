<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Git;

use Slothsoft\Devtools\Misc\Utils;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;
use Slothsoft\Devtools\Misc\Update\Project;

class DeleteBranches implements UpdateInterface {

    private array $branches = [
        'develop',
        'master',
        'dependabot/github_actions/dot-github/workflows/actions/download-artifact-4.1.7'
    ];

    public function runOn(Project $project) {
        if ($project->chdir() and is_dir('.git')) {
            foreach ($this->branches as $branch) {
                Utils::execute("git push origin :refs/heads/$branch");
                Utils::execute("git branch -D $branch");
            }
        }
    }
}

