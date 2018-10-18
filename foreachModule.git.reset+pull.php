<?php

use Slothsoft\Devtools\Update\Git\Reset;

$manager = include('src/foreachModule.php');

echo 'are you sure you want to RESET to last pushed commit?';
$message = stream_get_line(STDIN, 1024, PHP_EOL);

if ($message === 'yes') {
    $manager->run(new Reset());
} else {
    echo 'you did not answer "yes", aborting~';
}