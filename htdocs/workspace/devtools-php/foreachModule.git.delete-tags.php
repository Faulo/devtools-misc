<?php

use Slothsoft\Devtools\Update\Git\DeleteTags;

$manager = include('src/foreachModule.php');

$manager->run(new DeleteTags());