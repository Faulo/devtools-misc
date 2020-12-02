<?php
use Slothsoft\Devtools\ServerManager;

require_once __DIR__ . '/../vendor/autoload.php';

return new ServerManager(__DIR__ . '/../../', include ('servers.php'));