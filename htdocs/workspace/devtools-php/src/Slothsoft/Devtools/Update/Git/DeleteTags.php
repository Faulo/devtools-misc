<?php
namespace Slothsoft\Devtools\Update\Git;

use Slothsoft\Devtools\Update\UpdateInterface;

class DeleteTags implements UpdateInterface {

    public function runOn(array $project) {
        $tags = `git tag`;
        $tags = preg_split('~\s+~', $tags, null, PREG_SPLIT_NO_EMPTY);
        foreach ($tags as $tag) {
            passthru("git tag -d $tag");
            passthru("git push origin :refs/tags/$tag");
        }
    }
}

