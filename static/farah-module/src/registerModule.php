<?php
declare(strict_types = 1);

use Slothsoft\Farah\Module\Module;

Module::registerWithXmlManifestAndDefaultAssets('%1$s@%2$s', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'assets');

