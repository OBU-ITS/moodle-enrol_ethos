<?php

use enrol_ethos\repositories\db_course_repository;

require_once('../../../config.php');
require_once($CFG->libdir.'/weblib.php');

global $DB;

$repo = new db_course_repository($DB);
$courses = $repo->getAllCoursesWithCustomFieldData("test_field_1", "000-000");

echo "--------<br />";
foreach($courses as $course) {
    echo $course->fullname . "<br />";
    echo "--------<br />";
}