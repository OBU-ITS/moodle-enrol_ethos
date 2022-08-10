<?php

use core_course\customfield\course_handler;
use enrol_ethos\services\moodle\mdl_course_custom_field_service;

require_once('../../../config.php');
require_once($CFG->libdir.'/weblib.php');

$service = mdl_course_custom_field_service::getInstance();

$category = $service->ensureCustomFieldCategory("Test");

$service->ensureCustomField($category, "Test field", "test_field_2", PARAM_TEXT, 50, 200, course_handler::NOTVISIBLE);