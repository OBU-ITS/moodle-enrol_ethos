<?php

use enrol_ethos\services\moodle\mdl_course_custom_field_service;

require_once('../../../config.php');
require_once($CFG->libdir.'/weblib.php');

$service = mdl_course_custom_field_service::getInstance();
$service->ensureCustomFieldCategory("Test");