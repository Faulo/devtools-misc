<?php
namespace Slothsoft\Devtools\Unity;

require_once __DIR__ . '/../vendor/autoload.php';

$testatId = 6;
$testatMessage = "Fix Testat 05 (fix mario.GetCurrentJumpSpeed test)";
$testatDeleteTests = false;

$course = new UnityCourse('repositories.xml', 'results');

$course->pullRepositories();
if ($testatDeleteTests) {
    foreach ($course->getStudents(true) as $student) {
        $student->unity->deleteFolder('Assets/Tests');
    }
}
$course->requestTest('tests', $testatId, $testatMessage);