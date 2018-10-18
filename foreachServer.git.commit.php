<?php

use Slothsoft\Devtools\Update\Git\Commit;

$manager = include('src/foreachServer.php');

echo 'commit message: ';
$message = stream_get_line(STDIN, 1024, PHP_EOL);

$manager->run(new Commit($message));