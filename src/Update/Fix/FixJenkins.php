<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update\Fix;

use Slothsoft\Devtools\Misc\Jenkins\ServerInfo;
use Slothsoft\Devtools\Misc\Update\Project;
use Slothsoft\Devtools\Misc\Update\UpdateInterface;

class FixJenkins implements UpdateInterface {

    public function __construct() {}

    public bool $alwaysSave = false;

    public bool $clearActions = false;

    public array $properties = [];

    public array $elements = [];

    public function runOn(Project $project) {
        if ($project->chdir()) {
            if ($jenkins = ServerInfo::find('.', true)) {
                foreach ($jenkins->jobs as $job) {
                    if ($job->isFlowJob()) {
                        echo $job->path . PHP_EOL;

                        $hasChanged = $this->alwaysSave;

                        if ($this->clearActions) {
                            if ($job->clearActions()) {
                                $hasChanged = true;
                            }
                        }

                        if ($job->setProperties($this->properties)) {
                            $hasChanged = true;
                        }

                        if ($job->setElements($this->elements)) {
                            $hasChanged = true;
                        }

                        if ($hasChanged) {
                            $job->save();
                        }
                    }
                }
            }
        }
    }
}

