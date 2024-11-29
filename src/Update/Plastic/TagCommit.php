<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Plastic;

use Slothsoft\Devtools\Misc\Utils;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class TagCommit implements UpdateInterface {

    private string $attribute;

    private string $value;

    private string $condition;

    private ?string $comment;

    public function __construct(string $attribute, string $condition, ?string $value = null) {
        $this->attribute = $attribute;
        $this->condition = $condition;
        $this->value = $value ?? $attribute;
    }

    public function setComment(string $comment): void {
        $this->comment = $comment;
    }

    public function runOn(Project $project) {
        if ($project->chdir() and is_dir('.plastic')) {
            $command = sprintf('cm attribute create %s', escapeshellarg($this->attribute));
            Utils::execute($command);

            if ($this->comment !== null) {
                $command = sprintf('cm attribute edit %s %s', escapeshellarg($this->attribute), escapeshellarg($this->comment));
                Utils::execute($command);
            }

            $command = sprintf('cm find changesets where %s --format="{changesetid}" --nototal', escapeshellarg($this->condition));
            $ids = [];
            exec($command, $ids);
            foreach ($ids as $id) {
                if ($id = (int) $id) {
                    $command = sprintf('cm attribute set %s cs:%d %s', escapeshellarg($this->attribute), $id, escapeshellarg($this->value));
                    Utils::execute($command);
                }
            }
        }
    }
}

