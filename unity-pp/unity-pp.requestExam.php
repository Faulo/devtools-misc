<?php
namespace Slothsoft\Devtools\Unity;

require_once __DIR__ . '/../vendor/autoload.php';

$course = new UnityCourse('repositories.xml', 'results');

$course->resetRepositories();
$course->deleteFolder('Assets/Tests');
$course->requestTest('tests', 5, "Fix Testat 05 (improve movement test messages)");