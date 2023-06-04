<?php

use Slothsoft\Devtools\Update\Git\Release;

$manager = include('src/foreachModule.php');

echo 'release version: ';
$version = stream_get_line(STDIN, 1024, PHP_EOL);

$manager->run(new Release($version));