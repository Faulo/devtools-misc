<?php
namespace Slothsoft\Unity;

require_once __DIR__ . '/../vendor/autoload.php';

$course = new UnityCourse('repositories.xml', 'results');

$course->requestTest('tests', 2);