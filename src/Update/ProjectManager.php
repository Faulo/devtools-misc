<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update;

class ProjectManager extends Group {

    protected $workspaceDir;

    public function __construct(string $id, string $workspaceDir) {
        parent::__construct($id);
        $this->workspaceDir = realpath($workspaceDir) . DIRECTORY_SEPARATOR;
    }
}

