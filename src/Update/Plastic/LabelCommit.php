<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Plastic;

use Slothsoft\Devtools\Misc\Utils;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class LabelCommit implements UpdateInterface {

    private string $label;

    private string $condition;

    public function __construct(string $label, string $condition) {
        $this->label = $label;
        $this->condition = $condition;
    }

    public function runOn(Project $project) {
        if ($project->chdir() and is_dir('.plastic')) {
            $command = sprintf('cm find changesets where %s --format="{changesetid}" --nototal', escapeshellarg($this->condition));
            $ids = [];
            exec($command, $ids);
            foreach ($ids as $id) {
                if ($id = (int) $id) {
                    $command = sprintf('cm label create %s %d', escapeshellarg($this->label), $id);
                    Utils::execute($command);
                }
            }
        }
    }
}

