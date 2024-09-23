<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Git;

use Slothsoft\Devtools\Misc\Update\UpdateFactory;

class GitUpdateFactory extends UpdateFactory {

    public function __construct() {
        $this->updates['pull'] = new Pull();
        $this->updates['reset'] = new Reset();
        $this->updates['commit'] = new Commit('build: update files');
        $this->updates['push'] = new Push();
        $this->updates['delete-branches'] = new DeleteBranches();
        $this->updates['flow-init'] = new FlowInit();
        $this->updates['flow-patch'] = new FlowPatch(FlowPatch::PATCH_RELEASE, 'minor fixes');
        $this->updates['flow-minor'] = new FlowPatch(FlowPatch::MINOR_RELEASE, 'new features');
        $this->updates['flow-major'] = new FlowPatch(FlowPatch::MAJOR_RELEASE, 'major upgrade');
        $this->updates['tag-patch'] = new TagPatch(FlowPatch::PATCH_RELEASE, 'minor fixes');
    }
}

