<?php
namespace Slothsoft\Devtools\Unity;

require_once __DIR__ . '/../vendor/autoload.php';

$testatId = 6;
$testatMessage = "Fix Testat 01-06 (fix sometimes not finding prefab instances)";

$course = new UnityCourse('repositories.xml', 'results');

foreach ($course->getStudents(true) as $student) {
    $student->git->reset();
    $student->unity->deleteFolder('Assets/Tests');
}
$course->requestTest('tests', $testatId, $testatMessage);