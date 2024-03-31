<?php
declare(strict_types = 1);

use Slothsoft\Devtools\Misc\Update\UpdateDatabase;
use Slothsoft\Devtools\Misc\Update\GitUpdateFactory;

UpdateDatabase::instance()->updateFactories[] = new GitUpdateFactory();