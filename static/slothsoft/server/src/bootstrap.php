<?php
declare(strict_types = 1);
namespace $PHP_NAMESPACE;

use Slothsoft\Core\ServerEnvironment;
use Slothsoft\Farah\Dictionary;
use Slothsoft\Farah\Kernel;
use Slothsoft\Farah\Module\Module;

// @include __DIR__ . '/../../global/slothsoft.core.php';
// @include __DIR__ . '/../../global/slothsoft.core.xslt.php';
// @include __DIR__ . '/../../global/slothsoft.core.dbms.php';
// @include __DIR__ . '/../../global/slothsoft.farah.php';

$root = dirname(__DIR__);

ServerEnvironment::setRootDirectory($root);
ServerEnvironment::setCacheDirectory($root . DIRECTORY_SEPARATOR . 'cache');
ServerEnvironment::setLogDirectory($root . DIRECTORY_SEPARATOR . 'log');
ServerEnvironment::setDataDirectory($root . DIRECTORY_SEPARATOR . 'data');

Kernel::setCurrentSitemap('farah://$COMPOSER_VENDOR@$COMPOSER_MODULE/sitemap');
Kernel::setTrackingEnabled(false);
Dictionary::setSupportedLanguages('en-us');

Module::registerWithXmlManifestAndDefaultAssets('$COMPOSER_VENDOR@$COMPOSER_MODULE', $root . DIRECTORY_SEPARATOR . 'assets');
