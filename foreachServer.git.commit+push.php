<?php

use Slothsoft\Devtools\Update\Git\Commit;
use Slothsoft\Devtools\Update\Git\Push;

$manager = include('src/foreachServer.php');

echo 'commit message: ';
$message = stream_get_line(STDIN, 1024, PHP_EOL);

$manager->run(new Commit($message), new Push());