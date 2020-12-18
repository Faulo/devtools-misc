<?php
namespace Slothsoft\Devtools\Unity;

require_once __DIR__ . '/../vendor/autoload.php';

$course = new UnityCourse('repositories.xml', 'results');

foreach ($course->getStudents(true) as $student) {
    $student->git->pull();
    $student->runTests();
}
$course->writeReport('report.xml', 'report.xsl', 'report.xhtml');