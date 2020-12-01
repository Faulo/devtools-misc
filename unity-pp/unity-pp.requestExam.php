<?php
namespace Slothsoft\Unity;

require_once __DIR__ . '/../vendor/autoload.php';

$course = new UnityCourse('repositories.xml', 'results');

$course->deleteFolder('Assets/Tests');
$course->requestTest('tests', 4, "Create Testat 04");